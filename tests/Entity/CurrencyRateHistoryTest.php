<?php

namespace Tourze\CurrencyManageBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;

class CurrencyRateHistoryTest extends TestCase
{
    private CurrencyRateHistory $history;

    protected function setUp(): void
    {
        $this->history = new CurrencyRateHistory();
    }

    public function test_getId_initiallyNull(): void
    {
        $this->assertNull($this->history->getId());
    }

    public function test_setCurrencyCode_setsCurrencyCodeCorrectly(): void
    {
        $code = 'USD';

        $result = $this->history->setCurrencyCode($code);

        $this->assertSame($this->history, $result);
        $this->assertSame($code, $this->history->getCurrencyCode());
    }

    public function test_getCurrencyCode_initiallyEmpty(): void
    {
        $this->assertSame('', $this->history->getCurrencyCode());
    }

    public function test_setCurrencyName_setsCurrencyNameCorrectly(): void
    {
        $name = '美元';
        
        $result = $this->history->setCurrencyName($name);
        
        $this->assertSame($this->history, $result);
        $this->assertSame($name, $this->history->getCurrencyName());
    }

    public function test_getCurrencyName_initiallyEmpty(): void
    {
        $this->assertSame('', $this->history->getCurrencyName());
    }

    public function test_setCurrencySymbol_setsCurrencySymbolCorrectly(): void
    {
        $symbol = '$';
        
        $result = $this->history->setCurrencySymbol($symbol);
        
        $this->assertSame($this->history, $result);
        $this->assertSame($symbol, $this->history->getCurrencySymbol());
    }

    public function test_getCurrencySymbol_initiallyEmpty(): void
    {
        $this->assertSame('', $this->history->getCurrencySymbol());
    }

    public function test_setFlag_setsFlagCorrectly(): void
    {
        $flag = 'us';
        
        $result = $this->history->setFlag($flag);
        
        $this->assertSame($this->history, $result);
        $this->assertSame($flag, $this->history->getFlag());
    }

    public function test_getFlag_initiallyNull(): void
    {
        $this->assertNull($this->history->getFlag());
    }

    public function test_setFlag_withNull(): void
    {
        $this->history->setFlag(null);
        
        $this->assertNull($this->history->getFlag());
    }

    public function test_setRateToCny_setsRateCorrectly(): void
    {
        $rate = 7.0;
        
        $result = $this->history->setRateToCny($rate);
        
        $this->assertSame($this->history, $result);
        $this->assertSame($rate, $this->history->getRateToCny());
    }

    public function test_getRateToCny_initiallyZero(): void
    {
        $this->assertSame(0.0, $this->history->getRateToCny());
    }

    public function test_setRateToCny_withZero(): void
    {
        $this->history->setRateToCny(0.0);
        
        $this->assertSame(0.0, $this->history->getRateToCny());
    }

    public function test_setRateToCny_withNegativeValue(): void
    {
        $rate = -1.5;
        
        $this->history->setRateToCny($rate);
        
        $this->assertSame($rate, $this->history->getRateToCny());
    }

    public function test_setRateDate_setsDateCorrectly(): void
    {
        $date = new \DateTimeImmutable('2025-01-01');
        
        $result = $this->history->setRateDate($date);
        
        $this->assertSame($this->history, $result);
        $this->assertSame($date, $this->history->getRateDate());
    }

    public function test_setRateDate_withDateTimeImmutable(): void
    {
        $date = new \DateTimeImmutable('2025-01-01');
        
        $this->history->setRateDate($date);
        
        $this->assertSame($date, $this->history->getRateDate());
    }

    public function test_setCreatedAt_setsTimeCorrectly(): void
    {
        $time = new \DateTimeImmutable('2025-01-01 12:00:00');
        
        $result = $this->history->setCreatedAt($time);
        
        $this->assertSame($this->history, $result);
        $this->assertSame($time, $this->history->getCreatedAt());
    }

    public function test_getCreatedAt_initiallyNull(): void
    {
        $this->assertNull($this->history->getCreatedAt());
    }

    public function test_setCreatedAt_withNull(): void
    {
        $this->history->setCreatedAt(null);
        
        $this->assertNull($this->history->getCreatedAt());
    }

    public function test_toString_withoutId(): void
    {
        $this->history->setCurrencyName('美元');
        $this->history->setCurrencySymbol('$');
        $this->history->setRateDate(new \DateTimeImmutable('2025-01-01'));
        
        $result = $this->history->__toString();
        
        $this->assertSame('', $result);
    }

    public function test_toString_withValidData(): void
    {
        // 使用反射设置ID，模拟有ID的情况
        $reflection = new \ReflectionClass($this->history);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->history, 1);
        
        $this->history->setCurrencyName('美元');
        $this->history->setCurrencySymbol('$');
        $this->history->setRateDate(new \DateTimeImmutable('2025-01-01'));
        
        $result = $this->history->__toString();
        
        $this->assertSame('美元[$] - 2025-01-01', $result);
    }

    public function test_toString_withEmptyStrings(): void
    {
        $reflection = new \ReflectionClass($this->history);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->history, 1);
        
        $this->history->setCurrencyName('');
        $this->history->setCurrencySymbol('');
        $this->history->setRateDate(new \DateTimeImmutable('2025-01-01'));
        
        $result = $this->history->__toString();
        
        $this->assertSame('[] - 2025-01-01', $result);
    }

    public function test_fluentInterface_chainedCalls(): void
    {
        $date = new \DateTimeImmutable('2025-01-01');
        $time = new \DateTimeImmutable('2025-01-01 12:00:00');
        
        $result = $this->history
            ->setCurrencyCode('USD')
            ->setCurrencyName('美元')
            ->setCurrencySymbol('$')
            ->setFlag('us')
            ->setRateToCny(7.0)
            ->setRateDate($date)
            ->setCreatedAt($time);
        
        $this->assertSame($this->history, $result);
        $this->assertSame('USD', $this->history->getCurrencyCode());
        $this->assertSame('美元', $this->history->getCurrencyName());
        $this->assertSame('$', $this->history->getCurrencySymbol());
        $this->assertSame('us', $this->history->getFlag());
        $this->assertSame(7.0, $this->history->getRateToCny());
        $this->assertSame($date, $this->history->getRateDate());
        $this->assertSame($time, $this->history->getCreatedAt());
    }
} 