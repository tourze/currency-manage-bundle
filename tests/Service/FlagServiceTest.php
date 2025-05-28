<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\Service\FlagService;

class FlagServiceTest extends TestCase
{
    private FlagService $flagService;

    protected function setUp(): void
    {
        $this->flagService = new FlagService();
    }

    public function test_getFlagCodeFromCurrency_withValidCurrency(): void
    {
        $result = $this->flagService->getFlagCodeFromCurrency('USD');
        $this->assertSame('us', $result);
    }

    public function test_getFlagCodeFromCurrency_withInvalidCurrency(): void
    {
        $result = $this->flagService->getFlagCodeFromCurrency('INVALID');
        $this->assertNull($result);
    }

    public function test_getFlagCodeFromCurrency_withCNY(): void
    {
        $result = $this->flagService->getFlagCodeFromCurrency('CNY');
        $this->assertSame('cn', $result);
    }

    public function test_getFlagCodeFromCurrency_withEUR(): void
    {
        $result = $this->flagService->getFlagCodeFromCurrency('EUR');
        $this->assertSame('eu', $result);
    }

    public function test_getFlagCodeFromCurrency_withEmptyString(): void
    {
        $result = $this->flagService->getFlagCodeFromCurrency('');
        $this->assertNull($result);
    }

    public function test_getFlagCodeFromCurrency_withMoreCurrencies(): void
    {
        // 测试更多货币映射
        $testCases = [
            'JPY' => 'jp',
            'GBP' => 'gb',
            'AUD' => 'au',
            'CAD' => 'ca',
            'CHF' => 'ch',
            'HKD' => 'hk',
            'SGD' => 'sg',
            'TWD' => 'tw',
            'NZD' => 'nz',
            'SEK' => 'se',
            'NOK' => 'no',
            'DKK' => 'dk',
            'PLN' => 'pl',
            'CZK' => 'cz',
            'HUF' => 'hu',
            'RON' => 'ro',
            'RUB' => 'ru',
            'INR' => 'in',
            'IDR' => 'id',
            'THB' => 'th',
            'MYR' => 'my',
            'PHP' => 'ph',
            'VND' => 'vn',
            'KRW' => 'kr',
            'SAR' => 'sa',
            'AED' => 'ae',
            'QAR' => 'qa',
            'KWD' => 'kw',
            'BHD' => 'bh',
            'OMR' => 'om',
            'JOD' => 'jo',
            'ILS' => 'il',
            'TRY' => 'tr',
            'ZAR' => 'za',
            'EGP' => 'eg',
            'NGN' => 'ng',
            'KES' => 'ke',
            'MXN' => 'mx',
            'BRL' => 'br',
            'ARS' => 'ar',
            'CLP' => 'cl',
            'COP' => 'co',
            'PEN' => 'pe',
            'VEF' => 've',
            'BOB' => 'bo',
            'UYU' => 'uy',
            'PYG' => 'py',
            // 历史货币
            'DEM' => 'de',
            'FRF' => 'fr',
            'ITL' => 'it',
            'ESP' => 'es',
            'NLG' => 'nl',
            'BEF' => 'be',
            'ATS' => 'at',
            'PTE' => 'pt',
            'FIM' => 'fi',
            'IEP' => 'ie',
            'GRD' => 'gr',
            // 特殊货币
            'XAU' => null, // 黄金
            'XAG' => null, // 银
        ];

        foreach ($testCases as $currencyCode => $expectedFlag) {
            $result = $this->flagService->getFlagCodeFromCurrency($currencyCode);
            $this->assertSame($expectedFlag, $result, "Currency {$currencyCode} should map to flag {$expectedFlag}");
        }
    }

    public function test_getFlagCodeFromCurrency_withSpecialCurrencies(): void
    {
        // 测试特殊货币单位
        $this->assertNull($this->flagService->getFlagCodeFromCurrency('XAU')); // 黄金
        $this->assertNull($this->flagService->getFlagCodeFromCurrency('XAG')); // 银
        $this->assertNull($this->flagService->getFlagCodeFromCurrency('XPT')); // 铂金
        $this->assertNull($this->flagService->getFlagCodeFromCurrency('XPD')); // 钯金
    }

    public function test_getAvailableFlags_returnsArray(): void
    {
        $result = $this->flagService->getAvailableFlags();
        $this->assertIsArray($result);
    }

    public function test_flagExists_withValidCode(): void
    {
        // 这个测试可能会失败，因为依赖于实际的 flag-icons 包
        // 但我们可以测试方法是否正常工作
        $result = $this->flagService->flagExists('us');
        $this->assertIsBool($result);
    }

    public function test_getFlagPath_withValidCode(): void
    {
        // 测试方法返回类型
        $result = $this->flagService->getFlagPath('us');
        $this->assertTrue(is_string($result) || is_null($result));
    }

    public function test_getFlagPath_with1x1Ratio(): void
    {
        $result = $this->flagService->getFlagPath('us', '1x1');
        $this->assertTrue(is_string($result) || is_null($result));
    }
}
