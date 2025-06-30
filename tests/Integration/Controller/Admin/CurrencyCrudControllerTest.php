<?php

namespace Tourze\CurrencyManageBundle\Tests\Integration\Controller\Admin;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Tourze\CurrencyManageBundle\Controller\Admin\CurrencyCrudController;
use Tourze\CurrencyManageBundle\CurrencyManageBundle;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\IntegrationTestKernel\IntegrationTestKernel;

class CurrencyCrudControllerTest extends KernelTestCase
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
        $this->assertTrue(class_exists(CurrencyCrudController::class));
    }

    public function test_getEntityFqcn(): void
    {
        $controller = new CurrencyCrudController();
        $this->assertSame(Currency::class, $controller::getEntityFqcn());
    }

    public function test_configureCrud(): void
    {
        $container = static::getContainer();
        $controller = $container->get(CurrencyCrudController::class);
        
        $this->assertInstanceOf(CurrencyCrudController::class, $controller);
    }

    public function test_configureFields(): void
    {
        $controller = new CurrencyCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));
        
        $this->assertNotEmpty($fields);
        $this->assertCount(7, $fields);
    }

    public function test_configureActions(): void
    {
        $controller = new CurrencyCrudController();
        $reflection = new \ReflectionClass($controller);
        $this->assertTrue($reflection->hasMethod('configureActions'));
    }

    public function test_configureFilters(): void
    {
        $controller = new CurrencyCrudController();
        $reflection = new \ReflectionClass($controller);
        $this->assertTrue($reflection->hasMethod('configureFilters'));
    }
}