<?php

namespace Tourze\CurrencyManageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tourze\CurrencyManageBundle\Service\CurrencyRateService;

/**
 * 货币数据初始化
 */
class CurrencyFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly CurrencyRateService $currencyRateService,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 使用 Service 层初始化基础货币数据
        $this->currencyRateService->syncRates();

        // 可以在这里添加额外的初始化逻辑，比如记录日志
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class,
        ];
    }

    /**
     * 获取执行优先级
     */
    public function getOrder(): int
    {
        return 2;
    }
}
