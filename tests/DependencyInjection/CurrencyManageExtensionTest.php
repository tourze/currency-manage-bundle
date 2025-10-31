<?php

namespace Tourze\CurrencyManageBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\CurrencyManageBundle\DependencyInjection\CurrencyManageExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(CurrencyManageExtension::class)]
final class CurrencyManageExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private CurrencyManageExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new CurrencyManageExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.environment', 'test');
    }

    public function testLoadWithEmptyConfigs(): void
    {
        $this->extension->load([], $this->container);

        // 应该没有异常抛出，容器应该仍然可用
        $this->assertTrue(
            $this->container->hasDefinition('Tourze\CurrencyManageBundle\Service\CurrencyService')
            || $this->container->hasAlias('Tourze\CurrencyManageBundle\Service\CurrencyService')
        );
    }

    public function testExtensionAlias(): void
    {
        // Symfony Extension 的默认别名是类名去掉 Extension 后缀并转换为下划线格式
        $this->assertEquals('currency_manage', $this->extension->getAlias());
    }

    public function testConfigurationDirectoryExists(): void
    {
        $configDir = __DIR__ . '/../../src/Resources/config';
        $this->assertDirectoryExists($configDir);
    }

    public function testServicesYamlFileExists(): void
    {
        $servicesFile = __DIR__ . '/../../src/Resources/config/services.yaml';
        $this->assertFileExists($servicesFile);
    }
}
