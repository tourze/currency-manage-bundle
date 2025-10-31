<?php

namespace Tourze\CurrencyManageBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\GBT2659\Alpha2Code;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Country::class)]
final class CountryTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Country();
    }

    /**
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'code' => ['code', 'CN'];
        yield 'name' => ['name', '中国'];
        yield 'flagCode' => ['flagCode', 'cn'];
        yield 'valid' => ['valid', true];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
    }

    private Country $country;

    protected function setUp(): void
    {
        parent::setUp();
        $this->country = new Country();
    }

    public function testGetIdInitiallyNull(): void
    {
        $this->assertNull($this->country->getId());
    }

    public function testSetCodeSetsCodeCorrectly(): void
    {
        $code = 'CN';

        $this->country->setCode($code);

        $this->assertSame($code, $this->country->getCode());
    }

    public function testGetCodeInitiallyEmpty(): void
    {
        $this->assertSame('', $this->country->getCode());
    }

    public function testSetNameSetsNameCorrectly(): void
    {
        $name = '中国';

        $this->country->setName($name);

        $this->assertSame($name, $this->country->getName());
    }

    public function testGetNameInitiallyEmpty(): void
    {
        $this->assertSame('', $this->country->getName());
    }

    public function testSetFlagCodeSetsFlagCodeCorrectly(): void
    {
        $flagCode = 'cn';

        $this->country->setFlagCode($flagCode);

        $this->assertSame($flagCode, $this->country->getFlagCode());
    }

    public function testGetFlagCodeInitiallyNull(): void
    {
        $this->assertNull($this->country->getFlagCode());
    }

    public function testSetFlagCodeWithNull(): void
    {
        $this->country->setFlagCode(null);

        $this->assertNull($this->country->getFlagCode());
    }

    public function testSetValidSetsValidCorrectly(): void
    {
        $this->country->setValid(false);

        $this->assertFalse($this->country->isValid());
    }

    public function testIsValidInitiallyTrue(): void
    {
        $this->assertTrue($this->country->isValid());
    }

    public function testSetCreateTimeSetsTimeCorrectly(): void
    {
        $time = new \DateTimeImmutable('2025-01-01 12:00:00');

        $this->country->setCreateTime($time);

        $this->assertSame($time, $this->country->getCreateTime());
    }

    public function testGetCreateTimeInitiallyNull(): void
    {
        $this->assertNull($this->country->getCreateTime());
    }

    public function testSetUpdateTimeSetsTimeCorrectly(): void
    {
        $time = new \DateTimeImmutable('2025-01-01 12:00:00');

        $this->country->setUpdateTime($time);

        $this->assertSame($time, $this->country->getUpdateTime());
    }

    public function testGetUpdateTimeInitiallyNull(): void
    {
        $this->assertNull($this->country->getUpdateTime());
    }

    public function testGetCurrenciesInitiallyEmpty(): void
    {
        $currencies = $this->country->getCurrencies();

        $this->assertCount(0, $currencies);
    }

    public function testAddCurrencyAddsCurrencyCorrectly(): void
    {
        $currency = new Currency();

        $result = $this->country->addCurrency($currency);

        $this->assertSame($this->country, $result);
        $this->assertTrue($this->country->getCurrencies()->contains($currency));
        $this->assertSame($this->country, $currency->getCountry());
    }

    public function testAddCurrencyDoesNotAddDuplicate(): void
    {
        $currency = new Currency();

        $this->country->addCurrency($currency);
        $this->country->addCurrency($currency);

        $this->assertCount(1, $this->country->getCurrencies());
    }

    public function testRemoveCurrencyRemovesCurrencyCorrectly(): void
    {
        $currency = new Currency();
        $this->country->addCurrency($currency);

        $result = $this->country->removeCurrency($currency);

        $this->assertSame($this->country, $result);
        $this->assertFalse($this->country->getCurrencies()->contains($currency));
        $this->assertNull($currency->getCountry());
    }

    public function testRemoveCurrencyWithNonExistentCurrency(): void
    {
        $currency = new Currency();

        $result = $this->country->removeCurrency($currency);

        $this->assertSame($this->country, $result);
        $this->assertCount(0, $this->country->getCurrencies());
    }

    public function testFromAlpha2CodeCreatesCountryCorrectly(): void
    {
        $alpha2Code = Alpha2Code::CN;

        $country = Country::fromAlpha2Code($alpha2Code);

        $this->assertSame('CN', $country->getCode());
        $this->assertSame('中国', $country->getName());
        $this->assertSame('cn', $country->getFlagCode());
    }

    public function testGetAlpha2CodeReturnsCorrectEnum(): void
    {
        $this->country->setCode('CN');

        $alpha2Code = $this->country->getAlpha2Code();

        $this->assertSame(Alpha2Code::CN, $alpha2Code);
    }

    public function testGetAlpha2CodeWithInvalidCode(): void
    {
        $this->country->setCode('INVALID');

        $alpha2Code = $this->country->getAlpha2Code();

        $this->assertNull($alpha2Code);
    }

    public function testToStringWithoutId(): void
    {
        $this->country->setName('中国');
        $this->country->setCode('CN');

        $result = $this->country->__toString();

        $this->assertSame('', $result);
    }

    public function testToStringWithValidData(): void
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

    public function testFluentInterfaceChainedCalls(): void
    {
        $createTime = new \DateTimeImmutable('2025-01-01 10:00:00');
        $updateTime = new \DateTimeImmutable('2025-01-01 12:00:00');

        // 不再支持链式调用，需要分别调用每个setter
        $this->country->setCode('US');
        $this->country->setName('美国');
        $this->country->setFlagCode('us');
        $this->country->setValid(true);
        $this->country->setCreateTime($createTime);
        $this->country->setUpdateTime($updateTime);
        $this->assertSame('US', $this->country->getCode());
        $this->assertSame('美国', $this->country->getName());
        $this->assertSame('us', $this->country->getFlagCode());
        $this->assertTrue($this->country->isValid());
        $this->assertSame($createTime, $this->country->getCreateTime());
        $this->assertSame($updateTime, $this->country->getUpdateTime());
    }
}
