<?php

namespace Tourze\CurrencyManageBundle\Command;

use Carbon\Carbon;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tourze\CurrencyManageBundle\Repository\CurrencyRepository;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use Yiisoft\Json\Json;

#[AsCronTask('40 8 * * *')]
#[AsCommand(name: 'curreny-manage:update-rate', description: '更新汇率信息')]
class UpdateCurrencyRateCommand extends Command
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly HttpClientInterface $httpClient,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->httpClient->request('GET', 'https://api.exchangerate-api.com/v4/latest/CNY');
        $json = Json::decode($response->getContent());

        $models = $this->currencyRepository->findAll();
        foreach ($models as $model) {
            if (!isset($json['rates'][$model->getCode()])) {
                continue;
            }

            $model->setRateToCny($json['rates'][$model->getCode()]);
            $model->setUpdateTime(Carbon::createFromTimestamp($json['time_last_updated'], date_default_timezone_get()));
            $this->currencyRepository->save($model);
        }

        return Command::SUCCESS;
    }
}
