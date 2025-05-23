<?php

namespace Tourze\CurrencyManageBundle\Tests\DependencyInjection;

use CreditBundle\Service\CurrencyServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\CurrencyManageBundle\DependencyInjection\CurrencyManageExtension;

class CurrencyManageExtensionTest extends TestCase
{
    public function testLoad_registersServices(): void
    {
        $container = new ContainerBuilder();
        $extension = new CurrencyManageExtension();
        
        $extension->load([], $container);
        
        // 验证服务是否正确注册
        // 由于使用了resource配置服务，所以应该检查服务接口和实现类的类名
        $this->assertTrue($container->has('CreditBundle\Service\DefaultCurrencyServiceInterface'));
        $this->assertTrue($container->has('CreditBundle\Service\CurrencyManager'));
        
        // 验证服务别名
        // 由于使用了AsAlias属性，所以检查别名是否存在
        $this->assertTrue($container->hasAlias(CurrencyServiceInterface::class));
        
        // 获取所有服务定义，检查是否包含我们需要的服务
        $serviceIds = $container->getServiceIds();
        $this->assertContains('CreditBundle\Service\DefaultCurrencyServiceInterface', $serviceIds);
        $this->assertContains('CreditBundle\Service\CurrencyManager', $serviceIds);
    }
} 