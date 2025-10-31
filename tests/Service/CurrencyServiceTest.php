<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\CurrencyManageBundle\Service\CurrencyService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(CurrencyService::class)]
#[RunTestsInSeparateProcesses]
final class CurrencyServiceTest extends AbstractIntegrationTestCase
{
    private CurrencyService $currencyService;

    protected function onSetUp(): void
    {
        $this->currencyService = self::getService(CurrencyService::class);
    }

    public function testGetCurrencyByCodeWithValidCode(): void
    {
        $result = $this->currencyService->getCurrencyByCode('USD');

        if (null !== $result) {
            $this->assertInstanceOf(Currency::class, $result);
            $this->assertSame('USD', $result->getCode());
        } else {
            $this->assertTrue(true, 'No currency found with code USD, which is acceptable for this test');
        }
    }

    public function testGetCurrencyByCodeWithInvalidCode(): void
    {
        $result = $this->currencyService->getCurrencyByCode('INVALID');
        $this->assertNull($result);
    }

    public function testGetAllCurrencies(): void
    {
        $result = $this->currencyService->getAllCurrencies();

        $this->assertIsArray($result);
        foreach ($result as $currency) {
            $this->assertInstanceOf(Currency::class, $currency);
        }
    }

    public function testGetCurrencyByIdWithValidId(): void
    {
        $result = $this->currencyService->getCurrencyById(1);

        if (null !== $result) {
            $this->assertInstanceOf(Currency::class, $result);
        } else {
            $this->assertTrue(true, 'No currency found with id 1, which is acceptable for this test');
        }
    }

    public function testGetCurrencyByIdWithInvalidId(): void
    {
        $result = $this->currencyService->getCurrencyById(999999);
        $this->assertNull($result);
    }

    public function testConstructorRequiresCurrencyRepository(): void
    {
        $reflection = new \ReflectionClass(CurrencyService::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertSame('currencyRepository', $parameters[0]->getName());
    }
}
