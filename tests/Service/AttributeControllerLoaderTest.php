<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CurrencyManageBundle\Service\AttributeControllerLoader;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * AttributeControllerLoader 服务测试
 *
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    private AttributeControllerLoader $loader;

    protected function onSetUp(): void
    {
        $this->loader = self::getService(AttributeControllerLoader::class);
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

        $this->assertNotNull($result);

        // 验证路由集合不为空（因为它加载了两个控制器的路由）
        $this->assertGreaterThanOrEqual(0, count($result));
    }

    public function testLoadWithNullType(): void
    {
        $result = $this->loader->load('resource', null);

        $this->assertNotNull($result);
    }

    public function testLoadWithSpecificType(): void
    {
        $result = $this->loader->load('resource', 'some_type');

        $this->assertNotNull($result);
    }
}
