<?php

namespace Tourze\CurrencyManageBundle\Command;

use Carbon\Carbon;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tourze\CurrencyManageBundle\Entity\Currency as CurrencyEntity;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;
use Tourze\CurrencyManageBundle\Repository\CurrencyRateHistoryRepository;
use Tourze\CurrencyManageBundle\Repository\CurrencyRepository;
use Tourze\CurrencyManageBundle\Service\FlagService;
use Tourze\GBT12406\Currency;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use Yiisoft\Json\Json;

#[AsCronTask('40 8 * * *')]
#[AsCommand(name: 'curreny-manage:update-rate', description: '更新汇率信息')]
class UpdateCurrencyRateCommand extends Command
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly CurrencyRateHistoryRepository $historyRepository,
        private readonly HttpClientInterface $httpClient,
        private readonly FlagService $flagService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->httpClient->request('GET', 'https://api.exchangerate-api.com/v4/latest/CNY');
        $json = Json::decode($response->getContent());

        $updatedCount = 0;
        $historyCount = 0;
        $updateTime = Carbon::createFromTimestamp($json['time_last_updated'], date_default_timezone_get());
        $rateDate = $updateTime->clone()->setTime(0, 0, 0);

        // 遍历所有货币枚举值
        foreach (Currency::cases() as $currencyEnum) {
            $currencyCode = $currencyEnum->value;

            // 检查API响应中是否包含该货币的汇率
            if (!isset($json['rates'][$currencyCode])) {
                continue;
            }

            $rate = $json['rates'][$currencyCode];
            $currencyName = $currencyEnum->getLabel();
            $flagCode = $this->flagService->getFlagCodeFromCurrencyViaCountry($currencyCode);

            // 查找或创建货币实体
            $currencyEntity = $this->currencyRepository->findByCode($currencyCode);
            if (!$currencyEntity) {
                $currencyEntity = new CurrencyEntity();
                $currencyEntity->setCode($currencyCode);
                $currencyEntity->setName($currencyName);
                $currencyEntity->setSymbol($currencyCode);
            }

            // 更新汇率和时间
            $currencyEntity->setRateToCny($rate);
            $currencyEntity->setUpdateTime($updateTime);

            // 只persist，不立即flush
            $this->currencyRepository->save($currencyEntity, false);
            $updatedCount++;

            // 检查是否已存在当日的历史记录
            $existingHistory = $this->historyRepository->findByCurrencyAndDate($currencyCode, $rateDate);

            if (!$existingHistory) {
                // 创建历史汇率记录
                $history = new CurrencyRateHistory();
                $history->setCurrencyCode($currencyCode);
                $history->setCurrencyName($currencyName);
                $history->setCurrencySymbol($currencyCode);
                $history->setFlag($flagCode);
                $history->setRateToCny($rate);
                $history->setRateDate($rateDate);

                $this->historyRepository->save($history, false);
                $historyCount++;
            } else {
                // 更新已存在的历史记录
                $existingHistory->setRateToCny($rate);
                $existingHistory->setCurrencyName($currencyName);
                $existingHistory->setCurrencySymbol($currencyCode);
                $existingHistory->setFlag($flagCode);

                $this->historyRepository->save($existingHistory, false);
            }
        }

        // 批量提交所有更改
        if ($updatedCount > 0) {
            $this->currencyRepository->flush();
            $this->historyRepository->flush();
        }

        $output->writeln("成功更新了 {$updatedCount} 个货币的汇率信息");
        $output->writeln("成功记录了 {$historyCount} 条新的历史汇率数据");

        return Command::SUCCESS;
    }
}
