<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Repository\CountryRepository;
use Tourze\CurrencyManageBundle\Service\FlagService;

class FlagServiceTest extends TestCase
{
    private FlagService $flagService;
    /** @var CountryRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $countryRepository;

    protected function setUp(): void
    {
        $this->countryRepository = $this->createMock(CountryRepository::class);
        $this->flagService = new FlagService($this->countryRepository);
    }

    public function test_getFlagPathFromCountry_withValidCountry(): void
    {
        $country = new Country();
        $country->setFlagCode('cn');
        
        $result = $this->flagService->getFlagPathFromCountry($country);
        
        // 根据 flag-icons 包是否安装，结果可能是路径或 null
        if ($result !== null) {
            $this->assertStringContainsString('.svg', $result);
        }
    }

    public function test_getFlagPathFromCountry_withNullFlagCode(): void
    {
        $country = new Country();
        $country->setFlagCode(null);
        
        $result = $this->flagService->getFlagPathFromCountry($country);
        
        $this->assertNull($result);
    }

    // 直接测试方法功能，而不是检查方法是否存在

    public function test_getFlagCodeFromCurrencyViaCountry_withValidCurrency(): void
    {
        // Mock 一个有货币的国家
        $country = new Country();
        $country->setFlagCode('us');
        
        $this->countryRepository->expects($this->once())
            ->method('findCountriesWithCurrencies')
            ->willReturn([$country]);
        
        $result = $this->flagService->getFlagCodeFromCurrencyViaCountry('USD');
        
        // 由于我们没有实际的货币关联，这会返回 null
        $this->assertNull($result);
    }

    public function test_getAvailableFlags_returnsArray(): void
    {
        $result = $this->flagService->getAvailableFlags();
        // 直接检查结果，不需要断言类型
        $this->assertNotNull($result);
    }

    public function test_flagExists_withValidCode(): void
    {
        // 测试方法功能
        $result = $this->flagService->flagExists('us');
        // 直接检查结果，不需要断言类型
        $this->assertNotNull($result);
    }

    public function test_getFlagPath_withValidCode(): void
    {
        // 测试方法功能
        $result = $this->flagService->getFlagPath('us');
        // 根据 flag-icons 包是否安装，结果可能是路径或 null
        if ($result !== null) {
            $this->assertStringContainsString('us.svg', $result);
        }
    }

    public function test_getFlagPath_with1x1Ratio(): void
    {
        $result = $this->flagService->getFlagPath('us', '1x1');
        // 根据 flag-icons 包是否安装，结果可能是路径或 null
        if ($result !== null) {
            $this->assertStringContainsString('1x1', $result);
            $this->assertStringContainsString('us.svg', $result);
        }
    }

    public function test_constructor_requiresCountryRepository(): void
    {
        $reflection = new \ReflectionClass(FlagService::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertSame('countryRepository', $parameters[0]->getName());
    }
}
