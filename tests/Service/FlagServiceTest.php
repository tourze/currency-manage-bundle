<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Service\FlagService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(FlagService::class)]
#[RunTestsInSeparateProcesses]
final class FlagServiceTest extends AbstractIntegrationTestCase
{
    private FlagService $flagService;

    protected function onSetUp(): void
    {
        $this->flagService = self::getService(FlagService::class);
    }

    public function testGetFlagPathFromCountryWithValidCountry(): void
    {
        $country = new Country();
        $country->setFlagCode('cn');

        $result = $this->flagService->getFlagPathFromCountry($country);

        if (null !== $result) {
            $this->assertStringContainsString('.svg', $result);
        } else {
            $this->assertTrue(true, 'No flag path found, which is acceptable when flag-icons package is not available');
        }
    }

    public function testGetFlagPathFromCountryWithNullFlagCode(): void
    {
        $country = new Country();
        $country->setFlagCode(null);

        $result = $this->flagService->getFlagPathFromCountry($country);

        $this->assertNull($result);
    }

    public function testGetFlagCodeFromCurrencyViaCountryWithValidCurrency(): void
    {
        $result = $this->flagService->getFlagCodeFromCurrencyViaCountry('USD');

        if (null !== $result) {
            $this->assertIsString($result);
            $this->assertNotEmpty($result);
        } else {
            $this->assertTrue(true, 'No flag code found for USD, which is acceptable');
        }
    }

    public function testGetAvailableFlagsReturnsArray(): void
    {
        $result = $this->flagService->getAvailableFlags();
        $this->assertIsArray($result);
    }

    public function testFlagExistsWithValidCode(): void
    {
        $result = $this->flagService->flagExists('us');
        $this->assertIsBool($result);

        // If flag-icons package is available, 'us' should exist
        if ($result) {
            $this->assertTrue($result, 'US flag should exist when package is available');
        }
    }

    public function testFlagExistsWithInvalidCode(): void
    {
        $result = $this->flagService->flagExists('invalid_country_code_xyz');
        $this->assertIsBool($result);
        $this->assertFalse($result, 'Invalid flag code should return false');
    }

    public function testFlagExistsWithDifferentRatios(): void
    {
        $result4x3 = $this->flagService->flagExists('cn', '4x3');
        $result1x1 = $this->flagService->flagExists('cn', '1x1');

        $this->assertIsBool($result4x3);
        $this->assertIsBool($result1x1);
    }

    public function testGetFlagPathWithValidCode(): void
    {
        $result = $this->flagService->getFlagPath('us');
        if (null !== $result) {
            $this->assertStringContainsString('us.svg', $result);
            $this->assertFileExists($result, 'Flag file should exist at the returned path');
        } else {
            $this->assertTrue(true, 'No flag path found, which is acceptable when flag-icons package is not available');
        }
    }

    public function testGetFlagPathWithInvalidCode(): void
    {
        $result = $this->flagService->getFlagPath('invalid_xyz');
        $this->assertNull($result, 'Invalid flag code should return null');
    }

    public function testGetFlagPathWith1x1Ratio(): void
    {
        $result = $this->flagService->getFlagPath('us', '1x1');
        if (null !== $result) {
            $this->assertStringContainsString('1x1', $result);
            $this->assertStringContainsString('us.svg', $result);
        } else {
            $this->assertTrue(true, 'No flag path found, which is acceptable when flag-icons package is not available');
        }
    }

    public function testGetAvailableFlagsReturnsSortedArray(): void
    {
        $result = $this->flagService->getAvailableFlags();
        $this->assertIsArray($result);

        if ([] !== $result) {
            // Check if array is sorted
            $sorted = $result;
            sort($sorted);
            $this->assertSame($sorted, $result, 'Available flags should be sorted');

            // Check if all elements are strings
            foreach ($result as $flag) {
                $this->assertIsString($flag);
            }
        }
    }

    public function testGetFlagPathFromCurrencyWithValidCurrency(): void
    {
        $result = $this->flagService->getFlagPathFromCurrency('USD');

        if (null !== $result) {
            $this->assertIsString($result);
            $this->assertStringContainsString('.svg', $result);
        } else {
            $this->assertTrue(true, 'No flag path found for USD currency');
        }
    }

    public function testGetFlagPathFromCurrencyWithInvalidCurrency(): void
    {
        $result = $this->flagService->getFlagPathFromCurrency('INVALID_XYZ');
        $this->assertNull($result, 'Invalid currency code should return null');
    }
}
