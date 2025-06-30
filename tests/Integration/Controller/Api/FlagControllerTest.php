<?php

namespace Tourze\CurrencyManageBundle\Tests\Integration\Controller\Api;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\CurrencyManageBundle\Controller\Api\FlagController;
use Tourze\CurrencyManageBundle\CurrencyManageBundle;
use Tourze\CurrencyManageBundle\Service\FlagService;
use Tourze\IntegrationTestKernel\IntegrationTestKernel;

class FlagControllerTest extends KernelTestCase
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
                CurrencyManageBundle::class => ['all' => true],
            ],
            [
                'Tourze\CurrencyManageBundle\Entity' => dirname(__DIR__, 4) . '/src/Entity',
            ]
        );
    }

    public function test_controllerExists(): void
    {
        $this->assertTrue(class_exists(FlagController::class));
    }

    public function test_controllerCanBeInstantiated(): void
    {
        $container = static::getContainer();
        $flagService = $container->get(FlagService::class);
        
        $controller = new FlagController($flagService);
        $this->assertInstanceOf(FlagController::class, $controller);
    }

    public function test_invoke_throwsNotFoundException_whenFlagNotFound(): void
    {
        $container = static::getContainer();
        $flagService = $container->get(FlagService::class);
        
        $controller = new FlagController($flagService);
        
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Flag not found for code: invalid');
        
        $controller->__invoke('invalid');
    }

    public function test_invoke_returnsBinaryFileResponse_whenFlagExists(): void
    {
        $container = static::getContainer();
        $flagService = $container->get(FlagService::class);
        
        // 模拟存在的国旗代码，需要根据实际可用的国旗进行测试
        // 这里使用 cn 作为测试，因为通常中国国旗是存在的
        if ($flagService->flagExists('cn', '4x3')) {
            $controller = new FlagController($flagService);
            $response = $controller->__invoke('cn');
            
            $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $response);
            $this->assertSame('image/svg+xml', $response->headers->get('Content-Type'));
            $cacheControl = $response->headers->get('Cache-Control');
            $this->assertStringContainsString('public', $cacheControl);
            $this->assertStringContainsString('max-age=86400', $cacheControl);
        } else {
            $this->markTestSkipped('No 4x3 flags available for testing');
        }
    }

    public function test_controller_serviceRegistration(): void
    {
        $container = static::getContainer();
        $this->assertTrue($container->has(FlagController::class));
        
        $controller = $container->get(FlagController::class);
        $this->assertInstanceOf(FlagController::class, $controller);
    }
}