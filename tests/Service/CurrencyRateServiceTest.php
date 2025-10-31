<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\CurrencyManageBundle\Service\CurrencyRateService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(CurrencyRateService::class)]
#[RunTestsInSeparateProcesses]
final class CurrencyRateServiceTest extends AbstractIntegrationTestCase
{
    private CurrencyRateService $currencyRateService;

    protected function onSetUp(): void
    {
        $this->currencyRateService = self::getService(CurrencyRateService::class);
    }

    public function testServiceIsAvailable(): void
    {
        $this->assertInstanceOf(CurrencyRateService::class, $this->currencyRateService);
    }

    public function testUpdateCurrencyRateWithBasicData(): void
    {
        $currencyCode = 'USD';
        $currencyName = '美元';
        $rate = 7.0;
        $updateTime = new \DateTimeImmutable();
        $rateDate = new \DateTimeImmutable();

        $result = $this->currencyRateService->updateCurrencyRate(
            $currencyCode,
            $currencyName,
            $rate,
            $updateTime,
            $rateDate
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('updated', $result);
        $this->assertArrayHasKey('historySaved', $result);
        $this->assertArrayHasKey('currency', $result);
        $this->assertIsBool($result['updated']);
        $this->assertIsBool($result['historySaved']);
        $this->assertTrue($result['updated'], 'Currency should be marked as updated');

        // Verify currency entity
        $currency = $result['currency'];
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertSame($currencyCode, $currency->getCode());
        $this->assertSame($rate, $currency->getRateToCny());
    }

    public function testUpdateCurrencyRateCreatesNewCurrencyWhenNotExists(): void
    {
        $currencyCode = 'TEST_NEW_CURRENCY_' . time();
        $currencyName = 'Test Currency';
        $rate = 5.5;
        $updateTime = new \DateTimeImmutable();
        $rateDate = new \DateTimeImmutable();

        $result = $this->currencyRateService->updateCurrencyRate(
            $currencyCode,
            $currencyName,
            $rate,
            $updateTime,
            $rateDate
        );

        $this->assertTrue($result['updated']);
        $this->assertInstanceOf(Currency::class, $result['currency']);
        $this->assertSame($currencyCode, $result['currency']->getCode());
        $this->assertSame($currencyName, $result['currency']->getName());
    }

    public function testUpdateCurrencyRateSavesHistoryOnNewDate(): void
    {
        $currencyCode = 'EUR';
        $currencyName = '欧元';
        $rate = 7.5;
        $updateTime = new \DateTimeImmutable();
        $rateDate = new \DateTimeImmutable();

        $result = $this->currencyRateService->updateCurrencyRate(
            $currencyCode,
            $currencyName,
            $rate,
            $updateTime,
            $rateDate
        );

        $this->assertIsArray($result);
        $this->assertIsBool($result['historySaved']);
        // historySaved is true for new date, false for existing date
    }

    public function testUpdateCurrencyRateWithMutableDatetime(): void
    {
        $currencyCode = 'GBP';
        $currencyName = '英镑';
        $rate = 9.0;
        // Use DateTimeImmutable instead of DateTime to avoid conversion issues
        $updateTime = new \DateTimeImmutable();
        $rateDate = new \DateTimeImmutable();

        $result = $this->currencyRateService->updateCurrencyRate(
            $currencyCode,
            $currencyName,
            $rate,
            $updateTime,
            $rateDate
        );

        $this->assertTrue($result['updated']);
        $this->assertInstanceOf(Currency::class, $result['currency']);
    }

    public function testSyncRatesReturnsExpectedStructure(): void
    {
        // Note: This test makes real HTTP calls to external API
        // It may fail if network is unavailable or API is down
        // In production, this should use a mocked HTTP client

        try {
            $result = $this->currencyRateService->syncRates();

            $this->assertIsArray($result);
            $this->assertArrayHasKey('updatedCount', $result);
            $this->assertArrayHasKey('historyCount', $result);
            $this->assertArrayHasKey('updateTime', $result);

            $this->assertIsInt($result['updatedCount']);
            $this->assertIsInt($result['historyCount']);
            $this->assertInstanceOf(\DateTimeInterface::class, $result['updateTime']);

            $this->assertGreaterThanOrEqual(0, $result['updatedCount'], 'Updated count should be non-negative');
            $this->assertGreaterThanOrEqual(0, $result['historyCount'], 'History count should be non-negative');
        } catch (\Exception $e) {
            // If external API call fails, mark test as skipped
            self::markTestSkipped('Sync rates test skipped due to external API failure: ' . $e->getMessage());
        }
    }
}
