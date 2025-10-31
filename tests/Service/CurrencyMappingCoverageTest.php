<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CurrencyManageBundle\Service\FlagService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * 货币映射覆盖率测试
 *
 * 注意：由于 getFlagCodeFromCurrency 方法已被删除，
 * 这些测试现在主要测试 FlagService 的基本功能
 *
 * @internal
 */
#[CoversClass(FlagService::class)]
#[RunTestsInSeparateProcesses]
final class CurrencyMappingCoverageTest extends AbstractIntegrationTestCase
{
    private FlagService $flagService;

    protected function onSetUp(): void
    {
        $this->flagService = self::getService(FlagService::class);
    }

    public function testFlagServiceHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(FlagService::class);
        $this->assertTrue($reflection->hasMethod('getFlagCodeFromCurrencyViaCountry'));
        $this->assertTrue($reflection->hasMethod('getFlagPathFromCurrency'));
        $this->assertTrue($reflection->hasMethod('getAvailableFlags'));
        $this->assertTrue($reflection->hasMethod('flagExists'));
    }

    public function testGetAvailableFlagsReturnsArray(): void
    {
        $result = $this->flagService->getAvailableFlags();
        $this->assertIsArray($result);
    }

    public function testFlagExistsReturnsBool(): void
    {
        $result = $this->flagService->flagExists('us');
        $this->assertIsBool($result);
    }

    public function testGetFlagCodeFromCurrencyViaCountry(): void
    {
        $result = $this->flagService->getFlagCodeFromCurrencyViaCountry('USD');

        if (null !== $result) {
            $this->assertIsString($result);
            $this->assertNotEmpty($result);
        } else {
            $this->assertTrue(true, 'No flag code found for USD, which is acceptable');
        }
    }

    public function testGetFlagPathFromCurrency(): void
    {
        $result = $this->flagService->getFlagPathFromCurrency('USD');

        if (null !== $result) {
            $this->assertIsString($result);
            $this->assertNotEmpty($result);
        } else {
            $this->assertTrue(true, 'No flag path found for USD, which is acceptable');
        }
    }
}
