<?php

namespace Tourze\CurrencyManageBundle\Command;

use Carbon\Carbon;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tourze\CurrencyManageBundle\Entity\Currency as CurrencyEntity;
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

        // 遍历所有货币枚举值
        foreach (Currency::cases() as $currencyEnum) {
            $currencyCode = $currencyEnum->value;

            // 检查API响应中是否包含该货币的汇率
            if (!isset($json['rates'][$currencyCode])) {
                continue;
            }

            // 查找或创建货币实体
            $currencyEntity = $this->currencyRepository->findByCode($currencyCode);
            if (!$currencyEntity) {
                $currencyEntity = new CurrencyEntity();
                $currencyEntity->setCode($currencyCode);
                $currencyEntity->setName($currencyEnum->getLabel());
                // 设置默认符号，可以根据需要进一步完善
                $currencyEntity->setSymbol($currencyCode);
                // 设置默认国旗代码（基于货币代码推断）
                $currencyEntity->setFlag($this->flagService->getFlagCodeFromCurrency($currencyCode));
            }

            // 更新汇率和时间
            $currencyEntity->setRateToCny($json['rates'][$currencyCode]);
            $currencyEntity->setUpdateTime(Carbon::createFromTimestamp($json['time_last_updated'], date_default_timezone_get()));

            // 只persist，不立即flush
            $this->currencyRepository->save($currencyEntity, false);
            $updatedCount++;
        }

        // 批量提交所有更改
        if ($updatedCount > 0) {
            $this->currencyRepository->flush();
        }

        $output->writeln("成功更新了 {$updatedCount} 个货币的汇率信息");

        return Command::SUCCESS;
    }
}
