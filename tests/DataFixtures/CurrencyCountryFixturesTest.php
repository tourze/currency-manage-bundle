<?php

namespace Tourze\CurrencyManageBundle\Tests\DataFixtures;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\DataFixtures\CountryFixtures;
use Tourze\CurrencyManageBundle\DataFixtures\CurrencyCountryFixtures;
use Tourze\CurrencyManageBundle\Service\FlagService;

class CurrencyCountryFixturesTest extends TestCase
{
    public function test_load_methodExists(): void
    {
        $this->assertTrue(method_exists(CurrencyCountryFixtures::class, 'load'));
    }

    public function test_getDependencies_returnsCorrectDependencies(): void
    {
        /** @var FlagService $flagService */
        $flagService = $this->createMock(FlagService::class);
        $fixtures = new CurrencyCountryFixtures($flagService);
        
        $dependencies = $fixtures->getDependencies();
        
        $this->assertIsArray($dependencies);
        $this->assertContains(CountryFixtures::class, $dependencies);
    }

    public function test_getOrder_returnsCorrectOrder(): void
    {
        /** @var FlagService $flagService */
        $flagService = $this->createMock(FlagService::class);
        $fixtures = new CurrencyCountryFixtures($flagService);
        
        $this->assertSame(2, $fixtures->getOrder());
    }

    public function test_implementsDependentFixtureInterface(): void
    {
        /** @var FlagService $flagService */
        $flagService = $this->createMock(FlagService::class);
        $fixtures = new CurrencyCountryFixtures($flagService);
        
        $this->assertInstanceOf(\Doctrine\Common\DataFixtures\DependentFixtureInterface::class, $fixtures);
    }

    public function test_extendsFixture(): void
    {
        /** @var FlagService $flagService */
        $flagService = $this->createMock(FlagService::class);
        $fixtures = new CurrencyCountryFixtures($flagService);
        
        $this->assertInstanceOf(\Doctrine\Bundle\FixturesBundle\Fixture::class, $fixtures);
    }

    public function test_constructor_requiresFlagService(): void
    {
        $reflection = new \ReflectionClass(CurrencyCountryFixtures::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertSame('flagService', $parameters[0]->getName());
    }

    public function test_load_methodSignature(): void
    {
        $reflection = new \ReflectionMethod(CurrencyCountryFixtures::class, 'load');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertSame('manager', $parameters[0]->getName());
        
        // 检查返回类型
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', $returnType->__toString());
    }
} 