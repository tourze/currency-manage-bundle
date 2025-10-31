<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Entity\Currency;

/**
 * 预定义货币数据初始化
 * 创建常见的货币记录并关联到对应国家
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class CurrencyCountryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $currencyMappings = $this->getCurrencyCountryMappings();

        foreach ($currencyMappings as $currencyData) {
            $currency = new Currency();
            $currency->setCode($currencyData['code']);
            $currency->setName($currencyData['name']);
            if (isset($currencyData['symbol'])) {
                $currency->setSymbol($currencyData['symbol']);
            }

            // 通过引用获取对应的国家
            if (isset($currencyData['countryCode']) && $this->hasReference('country_' . $currencyData['countryCode'], Country::class)) {
                $country = $this->getReference('country_' . $currencyData['countryCode'], Country::class);
                $currency->setCountry($country);
            }

            $manager->persist($currency);

            // 为货币添加引用，供其他 Fixture 使用
            $this->addReference('currency_' . $currencyData['code'], $currency);
        }

        $manager->flush();
    }

    /**
     * 获取货币-国家映射数据
     *
     * @return array<int, array{code: string, name: string, symbol?: string, countryCode: ?string}>
     */
    private function getCurrencyCountryMappings(): array
    {
        return [
            ['code' => 'CNY', 'name' => '人民币', 'symbol' => '¥', 'countryCode' => 'CN'],
            ['code' => 'USD', 'name' => '美元', 'symbol' => '$', 'countryCode' => 'US'],
            ['code' => 'EUR', 'name' => '欧元', 'symbol' => '€', 'countryCode' => null], // 欧盟，无对应单一国家
            ['code' => 'JPY', 'name' => '日元', 'symbol' => '¥', 'countryCode' => 'JP'],
            ['code' => 'GBP', 'name' => '英镑', 'symbol' => '£', 'countryCode' => 'GB'],
            ['code' => 'HKD', 'name' => '港币', 'symbol' => 'HK$', 'countryCode' => 'HK'],
            ['code' => 'CAD', 'name' => '加拿大元', 'symbol' => 'CA$', 'countryCode' => 'CA'],
            ['code' => 'AUD', 'name' => '澳大利亚元', 'symbol' => 'A$', 'countryCode' => 'AU'],
            ['code' => 'SGD', 'name' => '新加坡元', 'symbol' => 'S$', 'countryCode' => 'SG'],
            ['code' => 'CHF', 'name' => '瑞士法郎', 'symbol' => 'CHF', 'countryCode' => 'CH'],
            ['code' => 'KRW', 'name' => '韩元', 'symbol' => '₩', 'countryCode' => 'KR'],
            ['code' => 'THB', 'name' => '泰铢', 'symbol' => '฿', 'countryCode' => 'TH'],
            ['code' => 'MYR', 'name' => '马来西亚林吉特', 'symbol' => 'RM', 'countryCode' => 'MY'],
            ['code' => 'PHP', 'name' => '菲律宾比索', 'symbol' => '₱', 'countryCode' => 'PH'],
            ['code' => 'INR', 'name' => '印度卢比', 'symbol' => '₹', 'countryCode' => 'IN'],
            ['code' => 'IDR', 'name' => '印尼盾', 'symbol' => 'Rp', 'countryCode' => 'ID'],
            ['code' => 'VND', 'name' => '越南盾', 'symbol' => '₫', 'countryCode' => 'VN'],
            ['code' => 'RUB', 'name' => '俄罗斯卢布', 'symbol' => '₽', 'countryCode' => 'RU'],
            ['code' => 'BRL', 'name' => '巴西雷亚尔', 'symbol' => 'R$', 'countryCode' => 'BR'],
            ['code' => 'MXN', 'name' => '墨西哥比索', 'symbol' => 'Mex$', 'countryCode' => 'MX'],
        ];
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
