<?php

namespace Tourze\CurrencyManageBundle\Tests\Integration\Controller\Admin;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Tourze\CurrencyManageBundle\Controller\Admin\CurrencyRateHistoryCrudController;
use Tourze\CurrencyManageBundle\CurrencyManageBundle;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;
use Tourze\IntegrationTestKernel\IntegrationTestKernel;

class CurrencyRateHistoryCrudControllerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected static function createKernel(array $options = []): IntegrationTestKernel
    {
        return new IntegrationTestKernel(
            $options['environment'] ?? 'test',
            $options['debug'] ?? false,
            [
                DoctrineBundle::class => ['all' => true],
                DoctrineFixturesBundle::class => ['all' => true],
                TwigBundle::class => ['all' => true],
                EasyAdminBundle::class => ['all' => true],
                CurrencyManageBundle::class => ['all' => true],
            ],
            [
                'Tourze\CurrencyManageBundle\Entity' => dirname(__DIR__, 4) . '/src/Entity',
            ]
        );
    }

    public function test_controllerExists(): void
    {
        $this->assertTrue(class_exists(CurrencyRateHistoryCrudController::class));
    }

    public function test_getEntityFqcn(): void
    {
        $controller = new CurrencyRateHistoryCrudController();
        $this->assertSame(CurrencyRateHistory::class, $controller::getEntityFqcn());
    }

    public function test_configureCrud(): void
    {
        $container = static::getContainer();
        $controller = $container->get(CurrencyRateHistoryCrudController::class);
        
        $this->assertInstanceOf(CurrencyRateHistoryCrudController::class, $controller);
    }

    public function test_configureFields(): void
    {
        $controller = new CurrencyRateHistoryCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));
        
        $this->assertNotEmpty($fields);
        $this->assertCount(8, $fields);
    }

    public function test_configureActions(): void
    {
        $controller = new CurrencyRateHistoryCrudController();
        $reflection = new \ReflectionClass($controller);
        $this->assertTrue($reflection->hasMethod('configureActions'));
    }

    public function test_configureFilters(): void
    {
        $controller = new CurrencyRateHistoryCrudController();
        $reflection = new \ReflectionClass($controller);
        $this->assertTrue($reflection->hasMethod('configureFilters'));
    }
}