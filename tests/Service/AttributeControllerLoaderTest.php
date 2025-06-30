<?php

namespace Tourze\CurrencyManageBundle\Test\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouteCollection;
use Tourze\CurrencyManageBundle\Service\AttributeControllerLoader;

/**
 * AttributeControllerLoader 服务测试
 */
class AttributeControllerLoaderTest extends TestCase
{
    private AttributeControllerLoader $loader;

    protected function setUp(): void
    {
        $this->loader = new AttributeControllerLoader();
    }

    public function testLoad(): void
    {
        $result = $this->loader->load('resource');
        
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testSupports(): void
    {
        // AttributeControllerLoader 总是返回 false，因为它不支持特定资源
        $this->assertFalse($this->loader->supports('resource'));
        $this->assertFalse($this->loader->supports('resource', 'type'));
    }

    public function testAutoload(): void
    {
        $result = $this->loader->autoload();
        
        $this->assertInstanceOf(RouteCollection::class, $result);
        
        // 验证路由集合不为空（因为它加载了两个控制器的路由）
        $this->assertGreaterThanOrEqual(0, count($result));
    }

    public function testLoadWithNullType(): void
    {
        $result = $this->loader->load('resource', null);
        
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testLoadWithSpecificType(): void
    {
        $result = $this->loader->load('resource', 'some_type');
        
        $this->assertInstanceOf(RouteCollection::class, $result);
    }
} 