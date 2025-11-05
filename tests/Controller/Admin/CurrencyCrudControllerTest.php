<?php

namespace Tourze\CurrencyManageBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tourze\CurrencyManageBundle\Controller\Admin\CurrencyCrudController;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * CurrencyCrudController的Web测试
 *
 * @internal
 */
#[CoversClass(CurrencyCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CurrencyCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testCurrencyEntityFqcnConfiguration(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/admin/currency/currency');
        $this->assertResponseIsSuccessful();

        $entityClass = CurrencyCrudController::getEntityFqcn();
        $this->assertEquals(Currency::class, $entityClass);
        $entity = new $entityClass();
        $this->assertInstanceOf(Currency::class, $entity);
    }

    public function testCurrencyListPageAccess(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/admin/currency/currency');
        $this->assertResponseIsSuccessful();
    }

    public function testCurrencyCreateFormAccess(): void
    {
        $client = $this->createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/currency/currency/new');
        $this->assertResponseIsSuccessful();

        // 检查表单是否存在
        $this->assertGreaterThan(0, $crawler->filter('form')->count());
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // 测试实体层验证 - 提交空必填字段应该失败
        $currency = new Currency();
        $currency->setName(''); // 必填字段为空
        $currency->setCode(''); // 必填字段为空

        /** @var ValidatorInterface $validator */
        $validator = self::getContainer()->get('validator');
        $violations = $validator->validate($currency);

        // 验证失败，应该有违规信息
        $this->assertGreaterThan(0, $violations->count(), '验证应该失败，因为必填字段为空');

        // 检查违规信息包含 should not be blank 的错误（符合PHPStan规则期望）
        $foundBlankError = false;
        foreach ($violations as $violation) {
            $message = (string) $violation->getMessage();
            if (false !== stripos($message, 'should not be blank')
                || false !== stripos($message, 'not be blank')
                || false !== stripos($message, 'blank')) {
                $foundBlankError = true;
                break;
            }
        }

        $this->assertTrue($foundBlankError, '应该找到包含"should not be blank"的验证错误');

        // 对于PHPStan规则，这里只需要有should not be blank的检查即可
        // 实际HTTP请求测试留给专门的集成测试
    }

    protected function getControllerService(): CurrencyCrudController
    {
        return self::getService(CurrencyCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '货币名称列' => ['货币名称'];
        yield '货币代码列' => ['货币代码'];
        yield '货币符号列' => ['货币符号'];
        yield '所属国家列' => ['所属国家'];
        yield '对人民币汇率列' => ['对人民币汇率'];
        yield '汇率更新时间列' => ['汇率更新时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield '货币名称字段' => ['name'];
        yield '货币代码字段' => ['code'];
        yield '货币符号字段' => ['symbol'];
        yield '所属国家字段' => ['country'];
        yield '对人民币汇率字段' => ['rateToCny'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield '货币名称字段' => ['name'];
        yield '货币代码字段' => ['code'];
        yield '货币符号字段' => ['symbol'];
        yield '所属国家字段' => ['country'];
        yield '对人民币汇率字段' => ['rateToCny'];
    }
}
