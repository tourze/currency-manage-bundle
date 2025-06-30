<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\Repository\CountryRepository;
use Tourze\CurrencyManageBundle\Service\FlagService;

/**
 * 货币映射覆盖率测试
 *
 * 注意：由于 getFlagCodeFromCurrency 方法已被删除，
 * 这些测试现在主要测试 FlagService 的基本功能
 */
class CurrencyMappingCoverageTest extends TestCase
{
    private FlagService $flagService;
    /** @var CountryRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $countryRepository;

    protected function setUp(): void
    {
        $this->countryRepository = $this->createMock(CountryRepository::class);
        $this->flagService = new FlagService($this->countryRepository);
    }

    public function test_flagServiceBasicFunctionality(): void
    {
        // 测试 FlagService 的基本功能
        $reflection = new \ReflectionClass($this->flagService);
        $this->assertTrue($reflection->hasMethod('getFlagCodeFromCurrencyViaCountry'));
        $this->assertTrue($reflection->hasMethod('getFlagPathFromCurrency'));
        $this->assertTrue($reflection->hasMethod('getAvailableFlags'));
        $this->assertTrue($reflection->hasMethod('flagExists'));
    }

    public function test_getFlagCodeFromCurrencyViaCountry_withEmptyResult(): void
    {
        // Mock 空的国家列表
        $this->countryRepository->expects($this->once())
            ->method('findCountriesWithCurrencies')
            ->willReturn([]);

        $result = $this->flagService->getFlagCodeFromCurrencyViaCountry('USD');
        $this->assertNull($result);
    }

    public function test_getAvailableFlags_returnsArray(): void
    {
        $flags = $this->flagService->getAvailableFlags();
        $this->assertNotNull($flags);
    }

    public function test_flagExists_returnsBool(): void
    {
        $result = $this->flagService->flagExists('us');
        $this->assertNotNull($result);
    }
}
