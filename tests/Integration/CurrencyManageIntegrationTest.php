<?php

namespace Tourze\CurrencyManageBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Repository\CountryRepository;
use Tourze\CurrencyManageBundle\Service\FlagService;
use Tourze\GBT2659\Alpha2Code;

class CurrencyManageIntegrationTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }

    public function test_countryRepository_serviceExists(): void
    {
        $container = static::getContainer();
        
        $this->assertTrue($container->has(CountryRepository::class));
        
        $repository = $container->get(CountryRepository::class);
        $this->assertInstanceOf(CountryRepository::class, $repository);
    }

    public function test_flagService_serviceExists(): void
    {
        $container = static::getContainer();
        
        $this->assertTrue($container->has(FlagService::class));
        
        $service = $container->get(FlagService::class);
        $this->assertInstanceOf(FlagService::class, $service);
    }

    public function test_country_entityMapping(): void
    {
        $container = static::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        
        // 测试实体映射是否正确
        $metadata = $entityManager->getClassMetadata(Country::class);
        
        $this->assertSame(Country::class, $metadata->getName());
        $this->assertSame('starhome_country', $metadata->getTableName());
        
        // 检查字段映射
        $this->assertTrue($metadata->hasField('code'));
        $this->assertTrue($metadata->hasField('name'));
        $this->assertTrue($metadata->hasField('flagCode'));
        $this->assertTrue($metadata->hasField('valid'));
        $this->assertTrue($metadata->hasField('createTime'));
        $this->assertTrue($metadata->hasField('updateTime'));
        
        // 检查关联映射
        $this->assertTrue($metadata->hasAssociation('currencies'));
    }

    public function test_country_fromAlpha2Code_integration(): void
    {
        $country = Country::fromAlpha2Code(Alpha2Code::CN);
        
        $this->assertSame('CN', $country->getCode());
        $this->assertSame('中国', $country->getName());
        $this->assertSame('cn', $country->getFlagCode());
        $this->assertTrue($country->isValid());
    }

    public function test_flagService_integration(): void
    {
        $container = static::getContainer();
        $flagService = $container->get(FlagService::class);
        
        // 测试新方法存在
        $this->assertTrue(method_exists($flagService, 'getFlagPathFromCountry'));
        $this->assertTrue(method_exists($flagService, 'getFlagCodeFromCurrencyViaCountry'));
        $this->assertTrue(method_exists($flagService, 'getFlagPathFromCurrency'));
        $this->assertTrue(method_exists($flagService, 'getAvailableFlags'));
        $this->assertTrue(method_exists($flagService, 'flagExists'));
    }

    public function test_countryRepository_methods(): void
    {
        $container = static::getContainer();
        $repository = $container->get(CountryRepository::class);
        
        // 测试方法存在
        $this->assertTrue(method_exists($repository, 'findByCode'));
        $this->assertTrue(method_exists($repository, 'findByAlpha2Code'));
        $this->assertTrue(method_exists($repository, 'findAllValid'));
        $this->assertTrue(method_exists($repository, 'searchByName'));
        $this->assertTrue(method_exists($repository, 'findCountriesWithCurrencies'));
        $this->assertTrue(method_exists($repository, 'save'));
        $this->assertTrue(method_exists($repository, 'remove'));
        $this->assertTrue(method_exists($repository, 'flush'));
    }
} 