<?php

namespace Tourze\CurrencyManageBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\GBT2659\Alpha2Code;

class CountryTest extends TestCase
{
    private Country $country;

    protected function setUp(): void
    {
        $this->country = new Country();
    }

    public function test_getId_initiallyNull(): void
    {
        $this->assertNull($this->country->getId());
    }

    public function test_setCode_setsCodeCorrectly(): void
    {
        $code = 'CN';

        $result = $this->country->setCode($code);

        $this->assertSame($this->country, $result);
        $this->assertSame($code, $this->country->getCode());
    }

    public function test_getCode_initiallyEmpty(): void
    {
        $this->assertSame('', $this->country->getCode());
    }

    public function test_setName_setsNameCorrectly(): void
    {
        $name = '中国';
        
        $result = $this->country->setName($name);
        
        $this->assertSame($this->country, $result);
        $this->assertSame($name, $this->country->getName());
    }

    public function test_getName_initiallyEmpty(): void
    {
        $this->assertSame('', $this->country->getName());
    }

    public function test_setFlagCode_setsFlagCodeCorrectly(): void
    {
        $flagCode = 'cn';
        
        $result = $this->country->setFlagCode($flagCode);
        
        $this->assertSame($this->country, $result);
        $this->assertSame($flagCode, $this->country->getFlagCode());
    }

    public function test_getFlagCode_initiallyNull(): void
    {
        $this->assertNull($this->country->getFlagCode());
    }

    public function test_setFlagCode_withNull(): void
    {
        $this->country->setFlagCode(null);
        
        $this->assertNull($this->country->getFlagCode());
    }

    public function test_setValid_setsValidCorrectly(): void
    {
        $result = $this->country->setValid(false);
        
        $this->assertSame($this->country, $result);
        $this->assertFalse($this->country->isValid());
    }

    public function test_isValid_initiallyTrue(): void
    {
        $this->assertTrue($this->country->isValid());
    }

    public function test_setCreateTime_setsTimeCorrectly(): void
    {
        $time = new \DateTime('2025-01-01 12:00:00');
        
        $result = $this->country->setCreateTime($time);
        
        $this->assertSame($this->country, $result);
        $this->assertSame($time, $this->country->getCreateTime());
    }

    public function test_getCreateTime_initiallyNull(): void
    {
        $this->assertNull($this->country->getCreateTime());
    }

    public function test_setUpdateTime_setsTimeCorrectly(): void
    {
        $time = new \DateTime('2025-01-01 12:00:00');
        
        $result = $this->country->setUpdateTime($time);
        
        $this->assertSame($this->country, $result);
        $this->assertSame($time, $this->country->getUpdateTime());
    }

    public function test_getUpdateTime_initiallyNull(): void
    {
        $this->assertNull($this->country->getUpdateTime());
    }

    public function test_getCurrencies_initiallyEmpty(): void
    {
        $currencies = $this->country->getCurrencies();
        
        $this->assertCount(0, $currencies);
    }

    public function test_addCurrency_addsCurrencyCorrectly(): void
    {
        $currency = new Currency();
        
        $result = $this->country->addCurrency($currency);
        
        $this->assertSame($this->country, $result);
        $this->assertTrue($this->country->getCurrencies()->contains($currency));
        $this->assertSame($this->country, $currency->getCountry());
    }

    public function test_addCurrency_doesNotAddDuplicate(): void
    {
        $currency = new Currency();
        
        $this->country->addCurrency($currency);
        $this->country->addCurrency($currency);
        
        $this->assertCount(1, $this->country->getCurrencies());
    }

    public function test_removeCurrency_removesCurrencyCorrectly(): void
    {
        $currency = new Currency();
        $this->country->addCurrency($currency);
        
        $result = $this->country->removeCurrency($currency);
        
        $this->assertSame($this->country, $result);
        $this->assertFalse($this->country->getCurrencies()->contains($currency));
        $this->assertNull($currency->getCountry());
    }

    public function test_removeCurrency_withNonExistentCurrency(): void
    {
        $currency = new Currency();
        
        $result = $this->country->removeCurrency($currency);
        
        $this->assertSame($this->country, $result);
        $this->assertCount(0, $this->country->getCurrencies());
    }

    public function test_fromAlpha2Code_createsCountryCorrectly(): void
    {
        $alpha2Code = Alpha2Code::CN;
        
        $country = Country::fromAlpha2Code($alpha2Code);
        
        $this->assertSame('CN', $country->getCode());
        $this->assertSame('中国', $country->getName());
        $this->assertSame('cn', $country->getFlagCode());
    }

    public function test_getAlpha2Code_returnsCorrectEnum(): void
    {
        $this->country->setCode('CN');
        
        $alpha2Code = $this->country->getAlpha2Code();
        
        $this->assertSame(Alpha2Code::CN, $alpha2Code);
    }

    public function test_getAlpha2Code_withInvalidCode(): void
    {
        $this->country->setCode('INVALID');
        
        $alpha2Code = $this->country->getAlpha2Code();
        
        $this->assertNull($alpha2Code);
    }

    public function test_toString_withoutId(): void
    {
        $this->country->setName('中国');
        $this->country->setCode('CN');
        
        $result = $this->country->__toString();
        
        $this->assertSame('', $result);
    }

    public function test_toString_withValidData(): void
    {
        // 使用反射设置ID，模拟有ID的情况
        $reflection = new \ReflectionClass($this->country);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->country, 1);
        
        $this->country->setName('中国');
        $this->country->setCode('CN');
        
        $result = $this->country->__toString();
        
        $this->assertSame('中国[CN]', $result);
    }

    public function test_fluentInterface_chainedCalls(): void
    {
        $createTime = new \DateTime('2025-01-01 10:00:00');
        $updateTime = new \DateTime('2025-01-01 12:00:00');
        
        $result = $this->country
            ->setCode('US')
            ->setName('美国')
            ->setFlagCode('us')
            ->setValid(true)
            ->setCreateTime($createTime)
            ->setUpdateTime($updateTime);
        
        $this->assertSame($this->country, $result);
        $this->assertSame('US', $this->country->getCode());
        $this->assertSame('美国', $this->country->getName());
        $this->assertSame('us', $this->country->getFlagCode());
        $this->assertTrue($this->country->isValid());
        $this->assertSame($createTime, $this->country->getCreateTime());
        $this->assertSame($updateTime, $this->country->getUpdateTime());
    }
} 