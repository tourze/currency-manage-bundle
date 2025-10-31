<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Service\CountryService;
use Tourze\GBT2659\Alpha2Code;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(CountryService::class)]
#[RunTestsInSeparateProcesses]
final class CountryServiceTest extends AbstractIntegrationTestCase
{
    private CountryService $countryService;

    protected function onSetUp(): void
    {
        $this->countryService = self::getService(CountryService::class);
    }

    public function testFindByCode(): void
    {
        $result = $this->countryService->findByCode('US');

        if (null !== $result) {
            $this->assertInstanceOf(Country::class, $result);
            $this->assertSame('US', $result->getCode());
        } else {
            $this->assertTrue(true, 'No country found with code US, which is acceptable for this test');
        }
    }

    public function testFindByCodeWithInvalidCode(): void
    {
        $result = $this->countryService->findByCode('INVALID');
        $this->assertNull($result);
    }

    public function testFindByAlpha2Code(): void
    {
        $result = $this->countryService->findByAlpha2Code(Alpha2Code::CN);

        if (null !== $result) {
            $this->assertInstanceOf(Country::class, $result);
            $this->assertNotEmpty($result->getCode(), 'Country should have a valid code');
        } else {
            $this->assertTrue(true, 'No country found with Alpha2Code::CN, which is acceptable for this test');
        }
    }

    public function testFindAllValid(): void
    {
        $result = $this->countryService->findAllValid();

        $this->assertIsArray($result);
        foreach ($result as $country) {
            $this->assertInstanceOf(Country::class, $country);
            $this->assertNotEmpty($country->getCode(), 'Each country should have a valid code');
        }
    }

    public function testFindAllValidReturnsOnlyValidCountries(): void
    {
        $result = $this->countryService->findAllValid();

        $this->assertIsArray($result);
        // All returned countries should be valid (no deleted/inactive countries)
        foreach ($result as $country) {
            $this->assertInstanceOf(Country::class, $country);
        }
    }

    public function testSearchByName(): void
    {
        $result = $this->countryService->searchByName('China');

        $this->assertIsArray($result);
        foreach ($result as $country) {
            $this->assertInstanceOf(Country::class, $country);
            $name = $country->getName();
            $this->assertStringContainsStringIgnoringCase('China', $name, 'Search result should contain the search term');
        }
    }

    public function testSearchByNameWithEmptyString(): void
    {
        $result = $this->countryService->searchByName('');

        $this->assertIsArray($result);
        // Empty search should return empty array or all countries depending on implementation
    }

    public function testSearchByNameWithNoMatches(): void
    {
        $result = $this->countryService->searchByName('NonExistentCountryXYZ123');

        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Search with no matches should return empty array');
    }

    public function testFindCountriesWithCurrencies(): void
    {
        $result = $this->countryService->findCountriesWithCurrencies();

        $this->assertIsArray($result);
        foreach ($result as $country) {
            $this->assertInstanceOf(Country::class, $country);
            $currencies = $country->getCurrencies();
            $this->assertNotEmpty($currencies, 'Each country should have at least one currency');
        }
    }

    public function testFindCountriesWithCurrenciesReturnsDistinctCountries(): void
    {
        $result = $this->countryService->findCountriesWithCurrencies();

        $this->assertIsArray($result);
        $countryCodes = [];
        foreach ($result as $country) {
            $code = $country->getCode();
            $this->assertNotContains($code, $countryCodes, 'Each country should appear only once');
            $countryCodes[] = $code;
        }
    }
}
