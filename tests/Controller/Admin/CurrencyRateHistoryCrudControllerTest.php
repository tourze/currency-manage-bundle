<?php

namespace Tourze\CurrencyManageBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\CurrencyManageBundle\Controller\Admin\CurrencyRateHistoryCrudController;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * CurrencyRateHistoryCrudController的基本功能测试
 *
 * @internal
 */
#[CoversClass(CurrencyRateHistoryCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CurrencyRateHistoryCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testCurrencyRateHistoryEntityFqcnConfiguration(): void
    {
        $entityClass = CurrencyRateHistoryCrudController::getEntityFqcn();
        self::assertEquals(CurrencyRateHistory::class, $entityClass);
        $entity = new $entityClass();
        self::assertInstanceOf(CurrencyRateHistory::class, $entity);
    }

    public function testCurrencyRateHistoryListPageAccess(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/admin/currency/rate-history');
        $this->assertResponseIsSuccessful();
    }

    public function testCurrencyRateHistoryCreateFormAccessForbidden(): void
    {
        $client = $this->createAuthenticatedClient();

        // 期望抛出 ForbiddenActionException，表示NEW操作被禁用
        $this->expectException(ForbiddenActionException::class);
        $this->expectExceptionMessage('You don\'t have enough permissions to run the "new" action');

        $client->request('GET', '/admin/currency/rate-history/new');
    }

    public function testCurrencyRateHistoryEditFormAccessForbidden(): void
    {
        $client = $this->createAuthenticatedClient();

        // 期望抛出 ForbiddenActionException，表示EDIT操作被禁用
        $this->expectException(ForbiddenActionException::class);
        $this->expectExceptionMessage('You don\'t have enough permissions to run the "edit" action');

        // 尝试访问编辑页面（假设ID=1的记录存在）
        $client->request('GET', '/admin/currency/rate-history/1/edit');
    }

    public function testCurrencyRateHistoryDeleteActionForbidden(): void
    {
        $client = $this->createAuthenticatedClient();

        // 期望抛出 MethodNotAllowedHttpException，因为DELETE操作没有路由
        $this->expectException(MethodNotAllowedHttpException::class);
        $this->expectExceptionMessage('No route found for "DELETE');

        // 尝试删除操作（假设ID=1的记录存在）
        $client->request('DELETE', '/admin/currency/rate-history/1');
    }

    protected function getControllerService(): CurrencyRateHistoryCrudController
    {
        return self::getService(CurrencyRateHistoryCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '货币代码列' => ['货币代码'];
        yield '货币名称列' => ['货币名称'];
        yield '货币符号列' => ['货币符号'];
        yield '国旗代码列' => ['国旗代码'];
        yield '对人民币汇率列' => ['对人民币汇率'];
        yield '汇率日期列' => ['汇率日期'];
        yield '记录创建时间列' => ['记录创建时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        // 注意: CurrencyRateHistory 控制器禁用了 EDIT 操作
        // 提供虚拟数据以避免"Empty data set"错误，实际测试中会跳过
        yield '虚拟字段（EDIT操作已禁用）' => ['currencyCode'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        // 注意: CurrencyRateHistory 控制器禁用了 NEW 操作
        // 提供虚拟数据以避免"Empty data set"错误，实际测试中会跳过
        yield '虚拟字段（NEW操作已禁用）' => ['currencyCode'];
    }
}
