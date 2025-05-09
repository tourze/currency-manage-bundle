<?php

namespace Tourze\CurrencyManageBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\CurrencyManageBundle\Service\CurrencyManager;
use Tourze\CurrencyManageBundle\Service\CurrencyService;
use Tourze\CurrencyManageBundle\Service\CurrencyServiceImpl;

class CurrencyManageBundleIntegrationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }
    
    protected function setUp(): void
    {
        // 确保每个测试都有一致的环境变量设置
        $_ENV['DEFAULT_PRICE_PRECISION'] = 2;
        $_ENV['PRICE_PRECISION_STYLE'] = 'default';
    }
    
    public function testServiceWiring_currencyService(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        // 测试服务是否在容器中
        $this->assertTrue($container->has(CurrencyService::class));
        
        // 获取服务并验证其类型
        $service = $container->get(CurrencyService::class);
        $this->assertInstanceOf(CurrencyServiceImpl::class, $service);
        
        // 测试服务功能
        $currencies = iterator_to_array($service->getCurrencies());
        $this->assertCount(1, $currencies);
        $this->assertSame('CNY', $currencies[0]->getCurrencyCode());
        
        // 测试findByCode方法
        $currency = $service->findByCode('CNY');
        $this->assertNotNull($currency);
        $this->assertSame('CNY', $currency->getCurrencyCode());
        
        $currency = $service->findByCode('INVALID_CODE');
        $this->assertNull($currency);
    }
    
    public function testServiceWiring_currencyManager(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        // 测试服务是否在容器中
        $this->assertTrue($container->has(CurrencyManager::class));
        
        // 获取服务并验证其类型
        $manager = $container->get(CurrencyManager::class);
        $this->assertInstanceOf(CurrencyManager::class, $manager);
        
        // 测试genSelectData方法
        $data = $manager->genSelectData();
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals('CNY', $data[0]['value']);
        
        // 测试getCurrencyByCode方法
        $currency = $manager->getCurrencyByCode('CNY');
        $this->assertNotNull($currency);
        $this->assertSame('CNY', $currency->getCurrencyCode());
        
        // 测试getCurrencyName方法
        $name = $manager->getCurrencyName('CNY');
        $this->assertSame('元', $name);
        
        // 测试getPriceNumber方法
        $price = $manager->getPriceNumber(5.10);
        $this->assertSame('5.1', $price);
        
        // 测试getDisplayPrice方法
        $displayPrice = $manager->getDisplayPrice('CNY', 5.10);
        $this->assertSame('5.1元', $displayPrice);
    }
    
    public function testFunctionalUsage(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        
        $manager = $container->get(CurrencyManager::class);
        
        // 测试不同格式化风格
        $_ENV['PRICE_PRECISION_STYLE'] = 'default';
        $this->assertSame('5', $manager->getPriceNumber('5.00'));
        
        $_ENV['PRICE_PRECISION_STYLE'] = 'to-b';
        $_ENV['DEFAULT_PRICE_PRECISION'] = 2; // 确保精度设置正确
        $this->assertSame('5.00', $manager->getPriceNumber('5'));
        
        // 测试边界值和极端情况
        $this->assertSame('0.00', $manager->getPriceNumber(0));
        $this->assertSame('10000.00', $manager->getPriceNumber(10000));
        $this->assertSame('-5.00', $manager->getPriceNumber(-5));
        
        // 测试货币展示
        $this->assertSame('5.00元', $manager->getDisplayPrice('CNY', 5));
        $this->assertSame('5.00UNKNOWN', $manager->getDisplayPrice('UNKNOWN', 5));
    }
} 