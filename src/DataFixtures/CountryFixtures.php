<?php

namespace Tourze\CurrencyManageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\GBT2659\Alpha2Code;

/**
 * 国家数据初始化
 */
class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 遍历所有 Alpha2Code 枚举值，创建对应的国家记录
        foreach (Alpha2Code::cases() as $alpha2Code) {
            // 检查是否已存在该国家代码
            $existingCountry = $manager->getRepository(Country::class)
                ->findOneBy(['code' => $alpha2Code->value]);

            if ($existingCountry) {
                // 如果已存在，更新名称（可能有翻译更新）
                $existingCountry->setName($alpha2Code->getLabel());
                $existingCountry->setFlagCode(strtolower($alpha2Code->value));
                continue;
            }

            // 创建新的国家记录
            $country = Country::fromAlpha2Code($alpha2Code);
            $manager->persist($country);

            // 为常用国家添加引用，供其他 Fixture 使用
            $this->addReference('country_' . $alpha2Code->value, $country);
        }

        $manager->flush();
    }

    /**
     * 获取执行优先级，确保在其他 Fixture 之前执行
     */
    public function getOrder(): int
    {
        return 1;
    }
}
