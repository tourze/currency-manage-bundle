<?php

namespace Tourze\CurrencyManageBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Currency::class)]
final class CurrencyTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Currency();
    }

    /**
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'symbol' => ['symbol', '¥'];
        yield 'name' => ['name', '人民币'];
        yield 'code' => ['code', 'CNY'];
        yield 'rateToCny' => ['rateToCny', 1.0];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
    }

    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currency = new Currency();
    }

    public function testGetIdInitiallyNull(): void
    {
        $this->assertNull($this->currency->getId());
    }

    public function testSetSymbolSetsSymbolCorrectly(): void
    {
        $symbol = '¥';

        $this->currency->setSymbol($symbol);

        $this->assertSame($symbol, $this->currency->getSymbol());
    }

    public function testGetSymbolInitiallyNull(): void
    {
        $this->assertNull($this->currency->getSymbol());
    }

    public function testSetSymbolWithEmptyString(): void
    {
        $this->currency->setSymbol('');

        $this->assertSame('', $this->currency->getSymbol());
    }

    public function testSetSymbolWithMaxLength(): void
    {
        $symbol = str_repeat('¥', 32);

        $this->currency->setSymbol($symbol);

        $this->assertSame($symbol, $this->currency->getSymbol());
    }

    public function testSetNameSetsNameCorrectly(): void
    {
        $name = '人民币';

        $this->currency->setName($name);

        $this->assertSame($name, $this->currency->getName());
    }

    public function testGetNameInitiallyEmpty(): void
    {
        $this->assertSame('', $this->currency->getName());
    }

    public function testSetNameWithEmptyString(): void
    {
        $this->currency->setName('');

        $this->assertSame('', $this->currency->getName());
    }

    public function testSetCodeSetsCodeCorrectly(): void
    {
        $code = 'CNY';

        $this->currency->setCode($code);

        $this->assertSame($code, $this->currency->getCode());
    }

    public function testGetCodeInitiallyEmpty(): void
    {
        $this->assertSame('', $this->currency->getCode());
    }

    public function testSetCodeWithEmptyString(): void
    {
        $this->currency->setCode('');

        $this->assertSame('', $this->currency->getCode());
    }

    public function testSetRateToCnySetsRateCorrectly(): void
    {
        $rate = 1.0;

        $this->currency->setRateToCny($rate);

        $this->assertSame($rate, $this->currency->getRateToCny());
    }

    public function testGetRateToCnyInitiallyNull(): void
    {
        $this->assertNull($this->currency->getRateToCny());
    }

    public function testSetRateToCnyWithNull(): void
    {
        $this->currency->setRateToCny(null);

        $this->assertNull($this->currency->getRateToCny());
    }

    public function testSetRateToCnyWithZero(): void
    {
        $this->currency->setRateToCny(0.0);

        $this->assertSame(0.0, $this->currency->getRateToCny());
    }

    public function testSetRateToCnyWithNegativeValue(): void
    {
        $rate = -1.5;

        $this->currency->setRateToCny($rate);

        $this->assertSame($rate, $this->currency->getRateToCny());
    }

    public function testSetRateToCnyWithLargeValue(): void
    {
        $rate = 999999.99;

        $this->currency->setRateToCny($rate);

        $this->assertSame($rate, $this->currency->getRateToCny());
    }

    public function testSetRateToCnyWithPreciseValue(): void
    {
        $rate = 6.78901234;

        $this->currency->setRateToCny($rate);

        $this->assertSame($rate, $this->currency->getRateToCny());
    }

    public function testSetUpdateTimeSetsTimeCorrectly(): void
    {
        $time = new \DateTimeImmutable('2025-01-01 12:00:00');

        $this->currency->setUpdateTime($time);

        $this->assertSame($time, $this->currency->getUpdateTime());
    }

    public function testGetUpdateTimeInitiallyNull(): void
    {
        $this->assertNull($this->currency->getUpdateTime());
    }

    public function testSetUpdateTimeWithNull(): void
    {
        $this->currency->setUpdateTime(null);

        $this->assertNull($this->currency->getUpdateTime());
    }

    public function testSetUpdateTimeWithDateTimeImmutable(): void
    {
        $time = new \DateTimeImmutable('2025-01-01 12:00:00');

        $this->currency->setUpdateTime($time);

        $this->assertSame($time, $this->currency->getUpdateTime());
    }

    public function testToStringWithoutId(): void
    {
        $this->currency->setName('人民币');
        $this->currency->setSymbol('¥');

        $result = $this->currency->__toString();

        $this->assertSame('', $result);
    }

    public function testToStringWithValidData(): void
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

    public function testToStringWithNullName(): void
    {
        $reflection = new \ReflectionClass($this->currency);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->currency, 1);

        // 设置空名称来测试 toString 的处理
        $this->currency->setName('');

        $this->currency->setSymbol('¥');

        $result = $this->currency->__toString();

        $this->assertSame('[¥]', $result);
    }

    public function testToStringWithNullSymbol(): void
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

    public function testToStringWithEmptyStrings(): void
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

    public function testFluentInterfaceChainedCalls(): void
    {
        $time = new \DateTimeImmutable();

        // 不再支持链式调用，需要分别调用每个setter
        $this->currency->setSymbol('$');
        $this->currency->setName('美元');
        $this->currency->setCode('USD');
        $this->currency->setRateToCny(7.0);
        $this->currency->setUpdateTime($time); // setUpdateTime返回void
        $this->assertSame('$', $this->currency->getSymbol());
        $this->assertSame('美元', $this->currency->getName());
        $this->assertSame('USD', $this->currency->getCode());
        $this->assertSame(7.0, $this->currency->getRateToCny());
        $this->assertSame($time, $this->currency->getUpdateTime());
    }
}
