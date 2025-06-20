<?php

namespace Tourze\CurrencyManageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\CurrencyManageBundle\Service\FlagService;

/**
 * 货币与国家关联数据修复
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

        $associatedCount = 0;

        foreach ($currencies as $currency) {
            $currencyCode = $currency->getCode();
            if ($currencyCode === null || $currency->getCountry() !== null) {
                // 跳过已有国家关联的货币
                continue;
            }

            // 使用 FlagService 获取对应的国旗代码
            $flagCode = $this->flagService->getFlagCodeFromCurrencyViaCountry($currencyCode);
            if ($flagCode === null) {
                // 尝试根据货币代码映射常见国家
                $flagCode = $this->getCountryCodeByCurrency($currencyCode);
            }

            if ($flagCode !== null) {
                // 根据国旗代码查找对应的国家
                $country = $manager->getRepository(Country::class)
                    ->findOneBy(['flagCode' => $flagCode]);

                if ($country !== null) {
                    $currency->setCountry($country);
                    $manager->persist($currency);
                    $associatedCount++;
                }
            }
        }

        if ($associatedCount > 0) {
            $manager->flush();
        }
    }

    /**
     * 根据货币代码获取对应的国家代码
     */
    private function getCountryCodeByCurrency(string $currencyCode): ?string
    {
        $mapping = [
            'CNY' => 'cn',
            'USD' => 'us',
            'EUR' => 'eu',
            'JPY' => 'jp',
            'GBP' => 'gb',
            'HKD' => 'hk',
            'CAD' => 'ca',
            'AUD' => 'au',
            'SGD' => 'sg',
            'CHF' => 'ch',
            'TWD' => 'tw',
            'KRW' => 'kr',
            'THB' => 'th',
            'MYR' => 'my',
            'PHP' => 'ph',
            'INR' => 'in',
            'IDR' => 'id',
            'VND' => 'vn',
            'RUB' => 'ru',
            'BRL' => 'br',
            'MXN' => 'mx',
            'AED' => 'ae',
            'SAR' => 'sa',
            'ZAR' => 'za',
            'EGP' => 'eg',
            'TRY' => 'tr',
            'PLN' => 'pl',
            'CZK' => 'cz',
            'HUF' => 'hu',
            'RON' => 'ro',
            'BGN' => 'bg',
            'HRK' => 'hr',
            'DKK' => 'dk',
            'SEK' => 'se',
            'NOK' => 'no',
            'ISK' => 'is',
        ];

        return $mapping[$currencyCode] ?? null;
    }

    /**
     * 依赖于 CountryFixtures 和 CurrencyFixtures
     */
    public function getDependencies(): array
    {
        return [
            CountryFixtures::class,
            CurrencyFixtures::class,
        ];
    }

    /**
     * 获取执行优先级
     */
    public function getOrder(): int
    {
        return 3;
    }
}
