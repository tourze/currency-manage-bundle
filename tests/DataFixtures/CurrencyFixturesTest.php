<?php

namespace Tourze\CurrencyManageBundle\Tests\DataFixtures;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\DataFixtures\CountryFixtures;
use Tourze\CurrencyManageBundle\DataFixtures\CurrencyFixtures;
use Tourze\CurrencyManageBundle\Service\CurrencyRateService;

class CurrencyFixturesTest extends TestCase
{
    public function test_load_methodExists(): void
    {
        $this->assertTrue(method_exists(CurrencyFixtures::class, 'load'));
    }

    public function test_getDependencies_returnsCorrectDependencies(): void
    {        $currencyRateService = $this->createMock(CurrencyRateService::class);
        $fixtures = new CurrencyFixtures($currencyRateService);
        
        $dependencies = $fixtures->getDependencies();
        $this->assertContains(CountryFixtures::class, $dependencies);
    }

    public function test_getOrder_returnsCorrectOrder(): void
    {        $currencyRateService = $this->createMock(CurrencyRateService::class);
        $fixtures = new CurrencyFixtures($currencyRateService);
        
        $this->assertSame(2, $fixtures->getOrder());
    }

    public function test_implementsDependentFixtureInterface(): void
    {        $currencyRateService = $this->createMock(CurrencyRateService::class);
        $fixtures = new CurrencyFixtures($currencyRateService);
        
        $this->assertInstanceOf(\Doctrine\Common\DataFixtures\DependentFixtureInterface::class, $fixtures);
    }

    public function test_extendsFixture(): void
    {        $currencyRateService = $this->createMock(CurrencyRateService::class);
        $fixtures = new CurrencyFixtures($currencyRateService);
        
        $this->assertInstanceOf(\Doctrine\Bundle\FixturesBundle\Fixture::class, $fixtures);
    }

    public function test_constructor_requiresCurrencyRateService(): void
    {
        $reflection = new \ReflectionClass(CurrencyFixtures::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertSame('currencyRateService', $parameters[0]->getName());
    }

    public function test_load_methodSignature(): void
    {
        $reflection = new \ReflectionMethod(CurrencyFixtures::class, 'load');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertSame('manager', $parameters[0]->getName());
        
        // 检查返回类型
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', $returnType->__toString());
    }
} 