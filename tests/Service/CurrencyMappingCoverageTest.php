<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\Service\FlagService;
use Tourze\GBT12406\Currency;

class CurrencyMappingCoverageTest extends TestCase
{
    private FlagService $flagService;

    protected function setUp(): void
    {
        $this->flagService = new FlagService();
    }

    public function test_allGBT12406CurrenciesHaveMapping(): void
    {
        $unmappedCurrencies = [];
        $mappedCount = 0;
        $totalCount = 0;

        foreach (Currency::cases() as $currency) {
            $totalCount++;
            $currencyCode = $currency->value;
            $flagCode = $this->flagService->getFlagCodeFromCurrency($currencyCode);

            if ($flagCode === null) {
                $unmappedCurrencies[] = $currencyCode . ' (' . $currency->getLabel() . ')';
            } else {
                $mappedCount++;
            }
        }

        // 输出统计信息
        echo "\n";
        echo "Total currencies: {$totalCount}\n";
        echo "Mapped currencies: {$mappedCount}\n";
        echo "Unmapped currencies: " . count($unmappedCurrencies) . "\n";
        echo "Coverage: " . round(($mappedCount / $totalCount) * 100, 2) . "%\n";

        if (!empty($unmappedCurrencies)) {
            echo "\nUnmapped currencies:\n";
            foreach ($unmappedCurrencies as $currency) {
                echo "- {$currency}\n";
            }
        }

        // 我们期望至少有 60% 的覆盖率，因为有很多货币是历史货币或特殊单位
        $coveragePercentage = ($mappedCount / $totalCount) * 100;
        $this->assertGreaterThanOrEqual(60, $coveragePercentage, 
            "Currency mapping coverage should be at least 60%, got {$coveragePercentage}%");
    }

    public function test_mappedFlagsExist(): void
    {
        $missingFlags = [];
        $checkedFlags = [];

        foreach (Currency::cases() as $currency) {
            $currencyCode = $currency->value;
            $flagCode = $this->flagService->getFlagCodeFromCurrency($currencyCode);

            if ($flagCode && !in_array($flagCode, $checkedFlags)) {
                $checkedFlags[] = $flagCode;

                if (!$this->flagService->flagExists($flagCode)) {
                    $missingFlags[] = $flagCode;
                }
            }
        }

        $this->assertEmpty($missingFlags, 
            "The following flag files do not exist: " . implode(', ', $missingFlags));
    }

    public function test_commonCurrenciesAreMapped(): void
    {
        // 确保最常用的货币都有映射
        $commonCurrencies = [
            'CNY', 'USD', 'EUR', 'JPY', 'GBP', 'AUD', 'CAD', 'CHF',
            'HKD', 'SGD', 'TWD', 'NZD', 'SEK', 'NOK', 'DKK', 'PLN',
            'CZK', 'HUF', 'RON', 'RUB', 'INR', 'IDR', 'THB', 'MYR',
            'PHP', 'VND', 'KRW', 'SAR', 'AED', 'QAR', 'KWD', 'BHD',
            'OMR', 'JOD', 'ILS', 'TRY', 'ZAR', 'EGP', 'NGN', 'KES',
            'MXN', 'BRL', 'ARS', 'CLP', 'COP', 'PEN'
        ];

        foreach ($commonCurrencies as $currencyCode) {
            $flagCode = $this->flagService->getFlagCodeFromCurrency($currencyCode);
            $this->assertNotNull($flagCode, 
                "Common currency {$currencyCode} should have a flag mapping");
            $this->assertTrue($this->flagService->flagExists($flagCode), 
                "Flag {$flagCode} for currency {$currencyCode} should exist");
        }
    }
}
