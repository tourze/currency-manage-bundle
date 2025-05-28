<?php

namespace Tourze\CurrencyManageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\CurrencyManageBundle\Service\FlagService;

/**
 * 货币与国家关联数据初始化
 */
class CurrencyCountryFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly FlagService $flagService,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 获取所有现有的货币
        $currencies = $manager->getRepository(Currency::class)->findAll();

        foreach ($currencies as $currency) {
            $currencyCode = $currency->getCode();
            if (!$currencyCode) {
                continue;
            }

            // 使用 FlagService 获取对应的国旗代码
            $flagCode = $this->flagService->getFlagCodeFromCurrencyViaCountry($currencyCode);
            if (!$flagCode) {
                continue;
            }

            // 根据国旗代码查找对应的国家
            $country = $manager->getRepository(Country::class)
                ->findOneBy(['flagCode' => $flagCode]);

            if ($country && !$currency->getCountry()) {
                $currency->setCountry($country);
                $manager->persist($currency);
            }
        }

        $manager->flush();
    }

    /**
     * 依赖于 CountryFixtures
     */
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
