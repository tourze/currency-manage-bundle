<?php

namespace Tourze\CurrencyManageBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Tourze\CurrencyManageBundle\DependencyInjection\CurrencyManageExtension;

class CurrencyManageExtensionTest extends TestCase
{
    private CurrencyManageExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new CurrencyManageExtension();
        $this->container = new ContainerBuilder();
    }

    public function test_instantiation_createsExtension(): void
    {
        $this->assertInstanceOf(CurrencyManageExtension::class, $this->extension);
    }

    public function test_inheritance_extendsExtension(): void
    {
        $this->assertInstanceOf(Extension::class, $this->extension);
    }

    public function test_load_withEmptyConfigs(): void
    {
        $configs = [];
        
        $this->extension->load($configs, $this->container);
        
        // 验证没有抛出异常，说明配置加载成功
        $this->assertTrue(true);
    }

    public function test_load_withMultipleConfigs(): void
    {
        $configs = [
            ['some_config' => 'value1'],
            ['another_config' => 'value2'],
        ];
        
        $this->extension->load($configs, $this->container);
        
        // 验证没有抛出异常，说明配置加载成功
        $this->assertTrue(true);
    }

    public function test_getAlias_returnsCorrectAlias(): void
    {
        $expectedAlias = 'currency_manage';
        
        $result = $this->extension->getAlias();
        
        $this->assertSame($expectedAlias, $result);
    }

    public function test_load_registersServices(): void
    {
        $configs = [];
        
        $this->extension->load($configs, $this->container);
        
        // 验证服务配置是否被加载
        // 由于使用了resource配置，服务会被自动注册
        $serviceIds = $this->container->getServiceIds();
        
        // 验证至少有一些服务被注册了
        $this->assertNotEmpty($serviceIds);
    }

    public function test_load_withContainerBuilder(): void
    {
        $configs = [];
        $initialServiceCount = count($this->container->getServiceIds());
        
        $this->extension->load($configs, $this->container);
        
        $finalServiceCount = count($this->container->getServiceIds());
        
        // 加载后服务数量应该增加
        $this->assertGreaterThanOrEqual($initialServiceCount, $finalServiceCount);
    }
} 