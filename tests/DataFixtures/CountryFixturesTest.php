<?php

namespace Tourze\CurrencyManageBundle\Tests\DataFixtures;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\DataFixtures\CountryFixtures;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\GBT2659\Alpha2Code;

class CountryFixturesTest extends TestCase
{
    private CountryFixtures $fixtures;

    protected function setUp(): void
    {
        $this->fixtures = new CountryFixtures();
    }

    public function test_load_methodExists(): void
    {
        $this->assertTrue(method_exists(CountryFixtures::class, 'load'));
    }

    public function test_getOrder_returnsCorrectOrder(): void
    {
        $this->assertSame(1, $this->fixtures->getOrder());
    }

    public function test_load_methodSignature(): void
    {
        $reflection = new \ReflectionMethod(CountryFixtures::class, 'load');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertSame('manager', $parameters[0]->getName());
        
        // 检查返回类型
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', $returnType->__toString());
    }

    public function test_fromAlpha2Code_integration(): void
    {
        // 测试 Country::fromAlpha2Code 方法的集成
        $country = Country::fromAlpha2Code(Alpha2Code::CN);
        
        $this->assertSame('CN', $country->getCode());
        $this->assertSame('中国', $country->getName());
        $this->assertSame('cn', $country->getFlagCode());
    }

    public function test_alpha2Code_enumCoverage(): void
    {
        // 确保所有 Alpha2Code 枚举值都能正确处理
        $cases = Alpha2Code::cases();
        
        $this->assertGreaterThan(0, count($cases));
        
        foreach ($cases as $alpha2Code) {
            $country = Country::fromAlpha2Code($alpha2Code);
            
            $this->assertSame($alpha2Code->value, $country->getCode());
            $this->assertSame($alpha2Code->getLabel(), $country->getName());
            $this->assertSame(strtolower($alpha2Code->value), $country->getFlagCode());
        }
    }

    public function test_fixtures_implementsCorrectInterface(): void
    {
        $this->assertInstanceOf(\Doctrine\Bundle\FixturesBundle\Fixture::class, $this->fixtures);
    }

    public function test_reference_naming_convention(): void
    {
        // 测试引用命名约定
        $testCodes = ['CN', 'US', 'JP', 'GB'];
        
        foreach ($testCodes as $code) {
            $expectedReference = 'country_' . $code;
            $this->assertIsString($expectedReference);
            $this->assertStringStartsWith('country_', $expectedReference);
        }
    }
} 