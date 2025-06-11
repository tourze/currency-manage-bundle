<?php

namespace Tourze\CurrencyManageBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\CurrencyManageBundle\Service\CurrencyRateService;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask('40 8 * * *')]
#[AsCommand(name: 'curreny-manage:update-rate', description: '更新汇率信息')]
class UpdateCurrencyRateCommand extends Command
{
    public function __construct(
        private readonly CurrencyRateService $currencyRateService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->currencyRateService->syncRates();

            $output->writeln("成功更新了 {$result['updatedCount']} 个货币的汇率信息");
            $output->writeln("成功记录了 {$result['historyCount']} 条新的历史汇率数据");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln("<error>汇率同步失败：{$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
