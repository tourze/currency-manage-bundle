<?php

namespace Tourze\CurrencyManageBundle\Service;

use Composer\InstalledVersions;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Repository\CountryRepository;

/**
 * 国旗服务
 */
class FlagService
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
    ) {
    }

    /**
     * 获取国旗文件路径
     */
    public function getFlagPath(string $flagCode, string $ratio = '4x3'): ?string
    {
        $flagIconsPath = InstalledVersions::getInstallPath('lipis/flag-icons');

        if (!$flagIconsPath) {
            return null;
        }

        $flagFile = $flagIconsPath . '/flags/' . $ratio . '/' . strtolower($flagCode) . '.svg';

        return file_exists($flagFile) ? $flagFile : null;
    }

    /**
     * 根据国家实体获取国旗文件路径
     */
    public function getFlagPathFromCountry(Country $country, string $ratio = '4x3'): ?string
    {
        $flagCode = $country->getFlagCode();
        if (!$flagCode) {
            return null;
        }

        return $this->getFlagPath($flagCode, $ratio);
    }

    /**
     * 根据货币代码获取国旗文件路径（通过国家关联）
     */
    public function getFlagPathFromCurrency(string $currencyCode, string $ratio = '4x3'): ?string
    {
        // 通过 Country 实体获取国旗代码
        $flagCode = $this->getFlagCodeFromCurrencyViaCountry($currencyCode);
        if ($flagCode) {
            return $this->getFlagPath($flagCode, $ratio);
        }

        return null;
    }

    /**
     * 通过 Country 实体获取货币对应的国旗代码
     */
    public function getFlagCodeFromCurrencyViaCountry(string $currencyCode): ?string
    {
        // 这里需要通过货币代码查找对应的国家
        // 由于一个货币可能对应多个国家，我们取第一个有效的
        $countries = $this->countryRepository->findCountriesWithCurrencies();
        
        foreach ($countries as $country) {
            foreach ($country->getCurrencies() as $currency) {
                if ($currency->getCode() === $currencyCode) {
                    return $country->getFlagCode();
                }
            }
        }

        return null;
    }

    /**
     * 检查国旗是否存在
     */
    public function flagExists(string $flagCode, string $ratio = '4x3'): bool
    {
        return $this->getFlagPath($flagCode, $ratio) !== null;
    }

    /**
     * 获取所有可用的国旗代码
     */
    public function getAvailableFlags(string $ratio = '4x3'): array
    {
        $flagIconsPath = InstalledVersions::getInstallPath('lipis/flag-icons');

        if (!$flagIconsPath) {
            return [];
        }

        $flagsDir = $flagIconsPath . '/flags/' . $ratio;

        if (!is_dir($flagsDir)) {
            return [];
        }

        $flags = [];
        $files = glob($flagsDir . '/*.svg');

        foreach ($files as $file) {
            $flags[] = basename($file, '.svg');
        }

        sort($flags);

        return $flags;
    }
}
