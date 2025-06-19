<?php

namespace Tourze\CurrencyManageBundle\Service;

use Carbon\CarbonImmutable;
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
class CurrencyRateService
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly CurrencyRateHistoryRepository $historyRepository,
        private readonly HttpClientInterface $httpClient,
        private readonly FlagService $flagService,
    ) {
    }

    /**
     * 同步汇率数据
     */
    public function syncRates(): array
    {
        $response = $this->httpClient->request('GET', 'https://api.exchangerate-api.com/v4/latest/CNY');
        $json = Json::decode($response->getContent());

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
            $result = $this->updateCurrencyRate($currencyCode, $currencyEnum->getLabel(), $rate, $updateTime, $rateDate);

            if ($result['updated']) {
                $updatedCount++;
            }
            if ($result['historySaved']) {
                $historyCount++;
            }
        }

        // 批量提交所有更改
        if ($updatedCount > 0) {
            $this->currencyRepository->flush();
            $this->historyRepository->flush();
        }

        return [
            'updatedCount' => $updatedCount,
            'historyCount' => $historyCount,
            'updateTime' => $updateTime,
        ];
    }

    /**
     * 更新单个货币汇率
     */
    public function updateCurrencyRate(
        string $currencyCode,
        string $currencyName,
        float $rate,
        \DateTimeInterface $updateTime,
        \DateTimeInterface $rateDate
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
        $this->currencyRepository->save($currencyEntity, false);

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

            $this->historyRepository->save($history, false);
            $historySaved = true;
        } else {
            // 更新已存在的历史记录
            $existingHistory->setRateToCny($rate);
            $existingHistory->setCurrencyName($currencyName);
            $existingHistory->setCurrencySymbol($currencyCode);
            $existingHistory->setFlag($flagCode);

            $this->historyRepository->save($existingHistory, false);
        }

        return [
            'updated' => true,
            'historySaved' => $historySaved,
            'currency' => $currencyEntity,
        ];
    }
} 