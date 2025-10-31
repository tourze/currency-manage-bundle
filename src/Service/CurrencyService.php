<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\CurrencyManageBundle\Repository\CurrencyRepository;

/**
 * 货币查询服务
 */
#[Autoconfigure(public: true)]
readonly class CurrencyService
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
    ) {
    }

    /**
     * 根据货币代码查找货币
     */
    public function getCurrencyByCode(string $code): ?Currency
    {
        return $this->currencyRepository->findByCode($code);
    }

    /**
     * 获取所有货币
     *
     * @return Currency[]
     */
    public function getAllCurrencies(): array
    {
        return $this->currencyRepository->findAll();
    }

    /**
     * 根据ID查找货币
     */
    public function getCurrencyById(int $id): ?Currency
    {
        return $this->currencyRepository->find($id);
    }
}
