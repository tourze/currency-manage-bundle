<?php

namespace Tourze\CurrencyManageBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(CurrencyRateHistory::class)]
final class CurrencyRateHistoryTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new CurrencyRateHistory();
    }

    /**
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'currencyCode' => ['currencyCode', 'USD'];
        yield 'currencyName' => ['currencyName', '美元'];
        yield 'currencySymbol' => ['currencySymbol', '$'];
        yield 'flag' => ['flag', 'us'];
        yield 'rateToCny' => ['rateToCny', 7.0];
        yield 'rateDate' => ['rateDate', new \DateTimeImmutable()];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
    }

    private CurrencyRateHistory $history;

    protected function setUp(): void
    {
        parent::setUp();
        $this->history = new CurrencyRateHistory();
    }

    public function testGetIdInitiallyNull(): void
    {
        $this->assertNull($this->history->getId());
    }

    public function testSetCurrencyCodeSetsCurrencyCodeCorrectly(): void
    {
        $code = 'USD';

        $this->history->setCurrencyCode($code);

        $this->assertSame($code, $this->history->getCurrencyCode());
    }

    public function testGetCurrencyCodeInitiallyEmpty(): void
    {
        $this->assertSame('', $this->history->getCurrencyCode());
    }

    public function testSetCurrencyNameSetsCurrencyNameCorrectly(): void
    {
        $name = '美元';

        $this->history->setCurrencyName($name);

        $this->assertSame($name, $this->history->getCurrencyName());
    }

    public function testGetCurrencyNameInitiallyEmpty(): void
    {
        $this->assertSame('', $this->history->getCurrencyName());
    }

    public function testSetCurrencySymbolSetsCurrencySymbolCorrectly(): void
    {
        $symbol = '$';

        $this->history->setCurrencySymbol($symbol);

        $this->assertSame($symbol, $this->history->getCurrencySymbol());
    }

    public function testGetCurrencySymbolInitiallyEmpty(): void
    {
        $this->assertSame('', $this->history->getCurrencySymbol());
    }

    public function testSetFlagSetsFlagCorrectly(): void
    {
        $flag = 'us';

        $this->history->setFlag($flag);

        $this->assertSame($flag, $this->history->getFlag());
    }

    public function testGetFlagInitiallyNull(): void
    {
        $this->assertNull($this->history->getFlag());
    }

    public function testSetFlagWithNull(): void
    {
        $this->history->setFlag(null);

        $this->assertNull($this->history->getFlag());
    }

    public function testSetRateToCnySetsRateCorrectly(): void
    {
        $rate = 7.0;

        $this->history->setRateToCny($rate);

        $this->assertSame($rate, $this->history->getRateToCny());
    }

    public function testGetRateToCnyInitiallyZero(): void
    {
        $this->assertSame(0.0, $this->history->getRateToCny());
    }

    public function testSetRateToCnyWithZero(): void
    {
        $this->history->setRateToCny(0.0);

        $this->assertSame(0.0, $this->history->getRateToCny());
    }

    public function testSetRateToCnyWithNegativeValue(): void
    {
        $rate = -1.5;

        $this->history->setRateToCny($rate);

        $this->assertSame($rate, $this->history->getRateToCny());
    }

    public function testSetRateDateSetsDateCorrectly(): void
    {
        $date = new \DateTimeImmutable('2025-01-01');

        $this->history->setRateDate($date);

        $this->assertSame($date, $this->history->getRateDate());
    }

    public function testSetRateDateWithDateTimeImmutable(): void
    {
        $date = new \DateTimeImmutable('2025-01-01');

        $this->history->setRateDate($date);

        $this->assertSame($date, $this->history->getRateDate());
    }

    public function testSetCreateTimeSetsTimeCorrectly(): void
    {
        $time = new \DateTimeImmutable('2025-01-01 12:00:00');

        $this->history->setCreateTime($time);

        $this->assertSame($time, $this->history->getCreateTime());
    }

    public function testGetCreateTimeInitiallyNull(): void
    {
        $this->assertNull($this->history->getCreateTime());
    }

    public function testSetCreateTimeWithNull(): void
    {
        $this->history->setCreateTime(null);

        $this->assertNull($this->history->getCreateTime());
    }

    public function testToStringWithoutId(): void
    {
        $this->history->setCurrencyName('美元');
        $this->history->setCurrencySymbol('$');
        $this->history->setRateDate(new \DateTimeImmutable('2025-01-01'));

        $result = $this->history->__toString();

        $this->assertSame('', $result);
    }

    public function testToStringWithValidData(): void
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

    public function testToStringWithEmptyStrings(): void
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

    public function testFluentInterfaceChainedCalls(): void
    {
        $date = new \DateTimeImmutable('2025-01-01');
        $time = new \DateTimeImmutable('2025-01-01 12:00:00');

        // 不再支持链式调用，需要分别调用每个setter
        $this->history->setCurrencyCode('USD');
        $this->history->setCurrencyName('美元');
        $this->history->setCurrencySymbol('$');
        $this->history->setFlag('us');
        $this->history->setRateToCny(7.0);
        $this->history->setRateDate($date);
        $this->history->setCreateTime($time); // setCreateTime返回void
        $this->assertSame('USD', $this->history->getCurrencyCode());
        $this->assertSame('美元', $this->history->getCurrencyName());
        $this->assertSame('$', $this->history->getCurrencySymbol());
        $this->assertSame('us', $this->history->getFlag());
        $this->assertSame(7.0, $this->history->getRateToCny());
        $this->assertSame($date, $this->history->getRateDate());
        $this->assertSame($time, $this->history->getCreateTime());
    }
}
