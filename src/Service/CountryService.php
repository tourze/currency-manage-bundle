<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Repository\CountryRepository;
use Tourze\GBT2659\Alpha2Code;

/**
 * 国家服务
 */
#[Autoconfigure(public: true)]
readonly class CountryService
{
    public function __construct(
        private CountryRepository $countryRepository,
    ) {
    }

    /**
     * 根据国家代码查找国家
     */
    public function findByCode(string $code): ?Country
    {
        return $this->countryRepository->findByCode($code);
    }

    /**
     * 根据 Alpha2Code 枚举查找国家
     */
    public function findByAlpha2Code(Alpha2Code $alpha2Code): ?Country
    {
        return $this->countryRepository->findByAlpha2Code($alpha2Code);
    }

    /**
     * 获取所有有效的国家
     *
     * @return Country[]
     */
    public function findAllValid(): array
    {
        return $this->countryRepository->findAllValid();
    }

    /**
     * 根据名称搜索国家
     *
     * @return Country[]
     */
    public function searchByName(string $name): array
    {
        return $this->countryRepository->searchByName($name);
    }

    /**
     * 获取有货币的国家
     *
     * @return Country[]
     */
    public function findCountriesWithCurrencies(): array
    {
        return $this->countryRepository->findCountriesWithCurrencies();
    }
}
