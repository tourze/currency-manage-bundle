<?php

namespace Tourze\CurrencyManageBundle\Service;

use Brick\Money\Currency;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
interface CurrencyService
{
    /**
     * @return iterable<Currency>
     */
    public function getCurrencies(): iterable;

    /**
     * 读取币种
     */
    public function findByCode(string $currency): ?Currency;
}
