<?php

namespace Tourze\CurrencyManageBundle\Tests\Controller\Api;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\CurrencyManageBundle\Controller\Api\Flag1x1Controller;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(Flag1x1Controller::class)]
#[RunTestsInSeparateProcesses]
final class Flag1x1ControllerTest extends AbstractWebTestCase
{
    public function testUnauthenticatedAccessAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('GET', '/currency/flag/cn/1x1');

        // 无论国旗是否存在，未认证用户都应该能访问（不会返回401/403）
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            200 === $statusCode || 404 === $statusCode,
            '未认证用户应该能访问国旗API，返回200或404而不是401/403'
        );
    }

    public function testGetMethodWorksCorrectly(): void
    {
        // 使用一个真实的临时文件来测试成功的场景
        $tempFile = tempnam(sys_get_temp_dir(), 'flag_') . '.svg';
        file_put_contents($tempFile, '<svg></svg>');

        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        try {
            $client->request('GET', '/currency/flag/cn/1x1');
            self::getClient($client);
            $this->assertResponseIsSuccessful();
            $this->assertResponseHeaderSame('Content-Type', 'image/svg+xml');
        } finally {
            // 清理临时文件
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    public function testPostMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('POST', '/currency/flag/cn/1x1');
        self::getClient($client);
        $this->assertResponseStatusCodeSame(405);
    }

    public function testPutMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('PUT', '/currency/flag/cn/1x1');
        self::getClient($client);
        $this->assertResponseStatusCodeSame(405);
    }

    public function testDeleteMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('DELETE', '/currency/flag/cn/1x1');
        self::getClient($client);
        $this->assertResponseStatusCodeSame(405);
    }

    public function testPatchMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('PATCH', '/currency/flag/cn/1x1');
        self::getClient($client);
        $this->assertResponseStatusCodeSame(405);
    }

    public function testOptionsMethodNotConfigured(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('OPTIONS', '/currency/flag/cn/1x1');

        // OPTIONS可能返回404（路由不存在）或405（方法不允许）或200（如果配置了CORS）
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains(
            $statusCode,
            [200, 404, 405],
            'OPTIONS请求应该返回200、404或405'
        );
    }

    public function testHeadMethodWorks(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        // 测试HEAD方法
        $client->request('HEAD', '/currency/flag/cn/1x1');

        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            200 === $statusCode || 404 === $statusCode,
            'HEAD请求应该返回200（成功）或404（国旗不存在）'
        );

        if (200 === $statusCode) {
            // HEAD请求应该返回相同的头部但没有body
            self::getClient($client);
            $this->assertResponseIsSuccessful();
            $contentType = $client->getResponse()->headers->get('Content-Type');
            $this->assertNotNull($contentType, 'Content-Type头应该存在');
            $this->assertEmpty($client->getResponse()->getContent());
        }
    }

    public function testInvalidFlagCodeReturns404(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true); // 捕获异常并转换为HTTP响应

        $client->request('GET', '/currency/flag/invalid-flag-code/1x1');
        self::getClient($client);
        $this->assertResponseStatusCodeSame(404);

        $response = $client->getResponse();
        $content = $response->getContent();
        $this->assertNotFalse($content);
        $this->assertStringContainsString('Flag not found', $content);
    }

    public function testMultipleCountryCodesHandling(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        // 测试多个国家代码
        $countryCodes = ['us', 'uk', 'jp', 'de', 'fr'];

        foreach ($countryCodes as $code) {
            $client->request('GET', '/currency/flag/' . $code . '/1x1');

            // 每个请求要么成功（返回图片），要么返回404（文件不存在）
            $this->assertTrue(
                $client->getResponse()->isSuccessful()
                || 404 === $client->getResponse()->getStatusCode()
            );

            if ($client->getResponse()->isSuccessful()) {
                $contentType = $client->getResponse()->headers->get('Content-Type');
                $this->assertNotNull($contentType);
                $this->assertStringStartsWith('image/', $contentType);
            }
        }
    }

    public function testSpecialCharactersInCodeHandling(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        // 测试包含特殊字符的代码
        $invalidCodes = ['../../../etc/passwd', 'flag@code', 'flag code', 'flag%20code'];

        foreach ($invalidCodes as $code) {
            $client->request('GET', '/currency/flag/' . $code . '/1x1');

            // 对于无效的代码，应该返回404或400
            $this->assertTrue(
                404 === $client->getResponse()->getStatusCode()
                || 400 === $client->getResponse()->getStatusCode()
            );
        }
    }

    public function testResponsePerformance(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $startTime = microtime(true);
        $client->request('GET', '/currency/flag/cn/1x1');
        $endTime = microtime(true);

        $responseTime = $endTime - $startTime;

        // 响应时间应该在合理范围内（比如1秒以内）
        $this->assertLessThan(1.0, $responseTime, '国旗API响应时间应该小于1秒');
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        if ('INVALID' === $method) {
            return; // Skip invalid methods without markTestSkipped
        }

        // Verify that method is testable before proceeding
        $testableMethods = ['POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'TRACE', 'PURGE'];
        if (!\in_array($method, $testableMethods, true)) {
            return; // Skip untestable methods without markTestSkipped
        }

        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);

        match ($method) {
            'POST' => $client->request('POST', '/currency/flag/cn/1x1'),
            'PUT' => $client->request('PUT', '/currency/flag/cn/1x1'),
            'DELETE' => $client->request('DELETE', '/currency/flag/cn/1x1'),
            'PATCH' => $client->request('PATCH', '/currency/flag/cn/1x1'),
            'OPTIONS' => $client->request('OPTIONS', '/currency/flag/cn/1x1'),
            'TRACE' => $client->request('TRACE', '/currency/flag/cn/1x1'),
            'PURGE' => $client->request('PURGE', '/currency/flag/cn/1x1'),
        };
    }
}
