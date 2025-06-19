<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;
use Tourze\CurrencyManageBundle\Repository\CurrencyRateHistoryRepository;
use Tourze\CurrencyManageBundle\Repository\CurrencyRepository;
use Tourze\CurrencyManageBundle\Service\CurrencyRateService;
use Tourze\CurrencyManageBundle\Service\FlagService;

class CurrencyRateServiceTest extends TestCase
{
    private CurrencyRateService $service;
    private CurrencyRepository&MockObject $currencyRepository;
    private CurrencyRateHistoryRepository&MockObject $historyRepository;
    private HttpClientInterface&MockObject $httpClient;
    private FlagService&MockObject $flagService;

    protected function setUp(): void
    {
        $this->currencyRepository = $this->createMock(CurrencyRepository::class);
        $this->historyRepository = $this->createMock(CurrencyRateHistoryRepository::class);
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->flagService = $this->createMock(FlagService::class);

        $this->service = new CurrencyRateService(
            $this->currencyRepository,
            $this->historyRepository,
            $this->httpClient,
            $this->flagService
        );
    }

    public function test_syncRates_successfulSync(): void
    {
        $apiResponse = [
            'rates' => [
                'USD' => 7.0,
                'EUR' => 8.0,
                'CNY' => 1.0,
            ],
            'time_last_updated' => 1640995200, // 2022-01-01 00:00:00
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')
            ->willReturn(json_encode($apiResponse));

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.exchangerate-api.com/v4/latest/CNY')
            ->willReturn($response);

        $this->flagService->method('getFlagCodeFromCurrencyViaCountry')
            ->willReturn('us');

        // 模拟没有现有货币记录
        $this->currencyRepository->method('findByCode')
            ->willReturn(null);

        // 模拟没有历史记录
        $this->historyRepository->method('findByCurrencyAndDate')
            ->willReturn(null);

        $this->currencyRepository->expects($this->atLeastOnce())
            ->method('save');

        $this->historyRepository->expects($this->atLeastOnce())
            ->method('save');

        $this->currencyRepository->expects($this->once())
            ->method('flush');

        $this->historyRepository->expects($this->once())
            ->method('flush');

        $result = $this->service->syncRates();
        $this->assertArrayHasKey('updatedCount', $result);
        $this->assertArrayHasKey('historyCount', $result);
        $this->assertArrayHasKey('updateTime', $result);
        $this->assertGreaterThan(0, $result['updatedCount']);
    }

    public function test_updateCurrencyRate_withNewCurrency(): void
    {
        $currencyCode = 'USD';
        $currencyName = '美元';
        $rate = 7.0;
        $updateTime = new \DateTimeImmutable();
        $rateDate = new \DateTimeImmutable();

        $this->flagService->method('getFlagCodeFromCurrencyViaCountry')
            ->with($currencyCode)
            ->willReturn('us');

        $this->currencyRepository->method('findByCode')
            ->with($currencyCode)
            ->willReturn(null);

        $this->historyRepository->method('findByCurrencyAndDate')
            ->willReturn(null);

        $this->currencyRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Currency::class), false);

        $this->historyRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(CurrencyRateHistory::class), false);

        $result = $this->service->updateCurrencyRate(
            $currencyCode,
            $currencyName,
            $rate,
            $updateTime,
            $rateDate
        );
        $this->assertTrue($result['updated']);
        $this->assertTrue($result['historySaved']);
        $this->assertInstanceOf(Currency::class, $result['currency']);
    }

    public function test_updateCurrencyRate_withExistingCurrency(): void
    {
        $currencyCode = 'USD';
        $currencyName = '美元';
        $rate = 7.0;
        $updateTime = new \DateTimeImmutable();
        $rateDate = new \DateTimeImmutable();

        $existingCurrency = new Currency();
        $existingCurrency->setCode($currencyCode);
        $existingCurrency->setName($currencyName);
        $existingCurrency->setSymbol('$');

        $this->flagService->method('getFlagCodeFromCurrencyViaCountry')
            ->willReturn('us');

        $this->currencyRepository->method('findByCode')
            ->with($currencyCode)
            ->willReturn($existingCurrency);

        $this->historyRepository->method('findByCurrencyAndDate')
            ->willReturn(null);

        $this->currencyRepository->expects($this->once())
            ->method('save')
            ->with($existingCurrency, false);

        $this->historyRepository->expects($this->once())
            ->method('save');

        $result = $this->service->updateCurrencyRate(
            $currencyCode,
            $currencyName,
            $rate,
            $updateTime,
            $rateDate
        );

        $this->assertTrue($result['updated']);
        $this->assertTrue($result['historySaved']);
        $this->assertSame($existingCurrency, $result['currency']);
        $this->assertSame($rate, $existingCurrency->getRateToCny());
    }

    public function test_updateCurrencyRate_withExistingHistory(): void
    {
        $currencyCode = 'USD';
        $currencyName = '美元';
        $rate = 7.0;
        $updateTime = new \DateTimeImmutable();
        $rateDate = new \DateTimeImmutable();

        $existingHistory = new CurrencyRateHistory();
        $existingHistory->setCurrencyCode($currencyCode);
        $existingHistory->setRateToCny(6.5);

        $this->flagService->method('getFlagCodeFromCurrencyViaCountry')
            ->willReturn('us');

        $this->currencyRepository->method('findByCode')
            ->willReturn(null);

        $this->historyRepository->method('findByCurrencyAndDate')
            ->willReturn($existingHistory);

        $this->historyRepository->expects($this->once())
            ->method('save')
            ->with($existingHistory, false);

        $result = $this->service->updateCurrencyRate(
            $currencyCode,
            $currencyName,
            $rate,
            $updateTime,
            $rateDate
        );

        $this->assertTrue($result['updated']);
        $this->assertFalse($result['historySaved']);
        $this->assertSame($rate, $existingHistory->getRateToCny());
    }
} 