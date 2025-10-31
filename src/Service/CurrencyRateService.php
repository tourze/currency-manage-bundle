<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\Service;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tourze\CurrencyManageBundle\Entity\Currency as CurrencyEntity;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;
use Tourze\CurrencyManageBundle\Repository\CurrencyRateHistoryRepository;
use Tourze\CurrencyManageBundle\Repository\CurrencyRepository;
use Tourze\GBT12406\Currency;
use Yiisoft\Json\Json;

/**
 * 货币汇率同步服务
 */
#[WithMonologChannel(channel: 'currency_manage')]
readonly class CurrencyRateService
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private CurrencyRateHistoryRepository $historyRepository,
        private HttpClientInterface $httpClient,
        private FlagService $flagService,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 同步汇率数据
     *
     * @return array{updatedCount: int, historyCount: int, updateTime: \DateTimeInterface}
     */
    public function syncRates(): array
    {
        $startTime = microtime(true);
        $url = 'https://api.exchangerate-api.com/v4/latest/CNY';

        $this->logger->info('开始同步汇率数据', ['url' => $url]);

        try {
            // @audit-logged 已在方法中实现完整的审计日志记录（请求和响应）
            $response = $this->httpClient->request('GET', $url);
            $content = $response->getContent();
            /** @var array{time_last_updated: int, rates: array<string, float>} $json */
            $json = Json::decode($content);

            $requestTime = microtime(true) - $startTime;

            $this->logger->info('汇率API请求成功', [
                'url' => $url,
                'response_time_ms' => round($requestTime * 1000, 2),
                'response_size' => strlen($content),
                'status_code' => $response->getStatusCode(),
            ]);
        } catch (\Exception $e) {
            $requestTime = microtime(true) - $startTime;

            $this->logger->error('汇率API请求失败', [
                'url' => $url,
                'error' => $e->getMessage(),
                'request_time_ms' => round($requestTime * 1000, 2),
                'exception_class' => get_class($e),
            ]);

            throw $e;
        }

        $updatedCount = 0;
        $historyCount = 0;
        $updateTime = CarbonImmutable::createFromTimestamp($json['time_last_updated'], date_default_timezone_get());
        $rateDate = $updateTime->clone()->setTime(0, 0, 0);

        // 遍历所有货币枚举值
        foreach (Currency::cases() as $currencyEnum) {
            $currencyCode = $currencyEnum->value;

            // 检查API响应中是否包含该货币的汇率
            if (!isset($json['rates'][$currencyCode])) {
                continue;
            }

            $rate = $json['rates'][$currencyCode];
            \assert(\is_numeric($rate), sprintf('Currency rate for %s must be numeric, got %s', $currencyCode, get_debug_type($rate)));
            $rate = (float) $rate;
            $result = $this->updateCurrencyRate($currencyCode, $currencyEnum->getLabel(), $rate, $updateTime, $rateDate);

            if ($result['updated']) {
                ++$updatedCount;
            }
            if ($result['historySaved']) {
                ++$historyCount;
            }
        }

        // 批量提交所有更改
        if ($updatedCount > 0) {
            $this->entityManager->flush();
        }

        $totalTime = microtime(true) - $startTime;

        $this->logger->info('汇率同步完成', [
            'updated_count' => $updatedCount,
            'history_count' => $historyCount,
            'total_time_ms' => round($totalTime * 1000, 2),
            'update_time' => $updateTime->format('Y-m-d H:i:s'),
        ]);

        return [
            'updatedCount' => $updatedCount,
            'historyCount' => $historyCount,
            'updateTime' => $updateTime,
        ];
    }

    /**
     * 更新单个货币汇率
     *
     * @return array{updated: bool, historySaved: bool, currency: CurrencyEntity}
     */
    public function updateCurrencyRate(
        string $currencyCode,
        string $currencyName,
        float $rate,
        \DateTimeInterface $updateTime,
        \DateTimeInterface $rateDate,
    ): array {
        $flagCode = $this->flagService->getFlagCodeFromCurrencyViaCountry($currencyCode);

        // 查找或创建货币实体
        $currencyEntity = $this->currencyRepository->findByCode($currencyCode);
        if (null === $currencyEntity) {
            $currencyEntity = new CurrencyEntity();
            $currencyEntity->setCode($currencyCode);
            $currencyEntity->setName($currencyName);
            $currencyEntity->setSymbol($currencyCode);
        }

        // 更新汇率和时间
        $currencyEntity->setRateToCny($rate);
        $currencyEntity->setUpdateTime($updateTime instanceof \DateTimeImmutable ? $updateTime : \DateTimeImmutable::createFromInterface($updateTime));

        // 只persist，不立即flush
        $this->entityManager->persist($currencyEntity);

        // 检查是否已存在当日的历史记录
        $existingHistory = $this->historyRepository->findByCurrencyAndDate($currencyCode, $rateDate);
        $historySaved = false;

        if (null === $existingHistory) {
            // 创建历史汇率记录
            $history = new CurrencyRateHistory();
            $history->setCurrencyCode($currencyCode);
            $history->setCurrencyName($currencyName);
            $history->setCurrencySymbol($currencyCode);
            $history->setFlag($flagCode);
            $history->setRateToCny($rate);
            $history->setRateDate($rateDate instanceof \DateTimeImmutable ? $rateDate : \DateTimeImmutable::createFromInterface($rateDate));

            $this->entityManager->persist($history);
            $historySaved = true;
        } else {
            // 更新已存在的历史记录
            $existingHistory->setRateToCny($rate);
            $existingHistory->setCurrencyName($currencyName);
            $existingHistory->setCurrencySymbol($currencyCode);
            $existingHistory->setFlag($flagCode);

            $this->entityManager->persist($existingHistory);
        }

        return [
            'updated' => true,
            'historySaved' => $historySaved,
            'currency' => $currencyEntity,
        ];
    }
}
