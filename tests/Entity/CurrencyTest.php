<?php

namespace Tourze\CurrencyManageBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\Entity\Currency;

class CurrencyTest extends TestCase
{
    private Currency $currency;

    protected function setUp(): void
    {
        $this->currency = new Currency();
    }

    public function test_getId_initiallyNull(): void
    {
        $this->assertNull($this->currency->getId());
    }

    public function test_setSymbol_setsSymbolCorrectly(): void
    {
        $symbol = '¥';
        
        $result = $this->currency->setSymbol($symbol);
        
        $this->assertSame($this->currency, $result);
        $this->assertSame($symbol, $this->currency->getSymbol());
    }

    public function test_getSymbol_initiallyNull(): void
    {
        $this->assertNull($this->currency->getSymbol());
    }

    public function test_setSymbol_withEmptyString(): void
    {
        $this->currency->setSymbol('');
        
        $this->assertSame('', $this->currency->getSymbol());
    }

    public function test_setSymbol_withMaxLength(): void
    {
        $symbol = str_repeat('¥', 32);
        
        $this->currency->setSymbol($symbol);
        
        $this->assertSame($symbol, $this->currency->getSymbol());
    }

    public function test_setName_setsNameCorrectly(): void
    {
        $name = '人民币';
        
        $result = $this->currency->setName($name);
        
        $this->assertSame($this->currency, $result);
        $this->assertSame($name, $this->currency->getName());
    }

    public function test_getName_initiallyEmpty(): void
    {
        $this->assertSame('', $this->currency->getName());
    }

    public function test_setName_withEmptyString(): void
    {
        $this->currency->setName('');
        
        $this->assertSame('', $this->currency->getName());
    }

    public function test_setCode_setsCodeCorrectly(): void
    {
        $code = 'CNY';
        
        $result = $this->currency->setCode($code);
        
        $this->assertSame($this->currency, $result);
        $this->assertSame($code, $this->currency->getCode());
    }

    public function test_getCode_initiallyEmpty(): void
    {
        $this->assertSame('', $this->currency->getCode());
    }

    public function test_setCode_withEmptyString(): void
    {
        $this->currency->setCode('');
        
        $this->assertSame('', $this->currency->getCode());
    }

    public function test_setRateToCny_setsRateCorrectly(): void
    {
        $rate = 1.0;
        
        $result = $this->currency->setRateToCny($rate);
        
        $this->assertSame($this->currency, $result);
        $this->assertSame($rate, $this->currency->getRateToCny());
    }

    public function test_getRateToCny_initiallyNull(): void
    {
        $this->assertNull($this->currency->getRateToCny());
    }

    public function test_setRateToCny_withNull(): void
    {
        $this->currency->setRateToCny(null);
        
        $this->assertNull($this->currency->getRateToCny());
    }

    public function test_setRateToCny_withZero(): void
    {
        $this->currency->setRateToCny(0.0);
        
        $this->assertSame(0.0, $this->currency->getRateToCny());
    }

    public function test_setRateToCny_withNegativeValue(): void
    {
        $rate = -1.5;
        
        $this->currency->setRateToCny($rate);
        
        $this->assertSame($rate, $this->currency->getRateToCny());
    }

    public function test_setRateToCny_withLargeValue(): void
    {
        $rate = 999999.99;
        
        $this->currency->setRateToCny($rate);
        
        $this->assertSame($rate, $this->currency->getRateToCny());
    }

    public function test_setRateToCny_withPreciseValue(): void
    {
        $rate = 6.78901234;
        
        $this->currency->setRateToCny($rate);
        
        $this->assertSame($rate, $this->currency->getRateToCny());
    }

    public function test_setUpdateTime_setsTimeCorrectly(): void
    {
        $time = new \DateTime('2025-01-01 12:00:00');
        
        $result = $this->currency->setUpdateTime($time);
        
        $this->assertSame($this->currency, $result);
        $this->assertSame($time, $this->currency->getUpdateTime());
    }

    public function test_getUpdateTime_initiallyNull(): void
    {
        $this->assertNull($this->currency->getUpdateTime());
    }

    public function test_setUpdateTime_withNull(): void
    {
        $this->currency->setUpdateTime(null);
        
        $this->assertNull($this->currency->getUpdateTime());
    }

    public function test_setUpdateTime_withDateTimeImmutable(): void
    {
        $time = new \DateTimeImmutable('2025-01-01 12:00:00');
        
        $this->currency->setUpdateTime($time);
        
        $this->assertSame($time, $this->currency->getUpdateTime());
    }

    public function test_toString_withoutId(): void
    {
        $this->currency->setName('人民币');
        $this->currency->setSymbol('¥');
        
        $result = $this->currency->__toString();
        
        $this->assertSame('', $result);
    }

    public function test_toString_withValidData(): void
    {
        // 使用反射设置ID，模拟有ID的情况
        $reflection = new \ReflectionClass($this->currency);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->currency, 1);
        
        $this->currency->setName('人民币');
        $this->currency->setSymbol('¥');
        
        $result = $this->currency->__toString();
        
        $this->assertSame('人民币[¥]', $result);
    }

    public function test_toString_withNullName(): void
    {
        $reflection = new \ReflectionClass($this->currency);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->currency, 1);
        
        // 直接使用反射设置为null，因为setter不接受null
        $nameProperty = $reflection->getProperty('name');
        $nameProperty->setAccessible(true);
        $nameProperty->setValue($this->currency, null);
        
        $this->currency->setSymbol('¥');
        
        $result = $this->currency->__toString();
        
        $this->assertSame('[¥]', $result);
    }

    public function test_toString_withNullSymbol(): void
    {
        $reflection = new \ReflectionClass($this->currency);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->currency, 1);
        
        $this->currency->setName('人民币');
        
        // 直接使用反射设置为null，因为setter不接受null
        $symbolProperty = $reflection->getProperty('symbol');
        $symbolProperty->setAccessible(true);
        $symbolProperty->setValue($this->currency, null);
        
        $result = $this->currency->__toString();
        
        $this->assertSame('人民币[]', $result);
    }

    public function test_toString_withEmptyStrings(): void
    {
        $reflection = new \ReflectionClass($this->currency);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->currency, 1);
        
        $this->currency->setName('');
        $this->currency->setSymbol('');
        
        $result = $this->currency->__toString();
        
        $this->assertSame('[]', $result);
    }

    public function test_fluentInterface_chainedCalls(): void
    {
        $time = new \DateTime();
        
        $result = $this->currency
            ->setSymbol('$')
            ->setName('美元')
            ->setCode('USD')
            ->setRateToCny(7.0)
            ->setUpdateTime($time);
        
        $this->assertSame($this->currency, $result);
        $this->assertSame('$', $this->currency->getSymbol());
        $this->assertSame('美元', $this->currency->getName());
        $this->assertSame('USD', $this->currency->getCode());
        $this->assertSame(7.0, $this->currency->getRateToCny());
        $this->assertSame($time, $this->currency->getUpdateTime());
    }
} 