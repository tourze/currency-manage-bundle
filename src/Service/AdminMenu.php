<?php

namespace Tourze\CurrencyManageBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 货币管理菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (!$item->getChild('货币管理')) {
            $item->addChild('货币管理');
        }

        $currencyMenu = $item->getChild('货币管理');

        // 国家管理菜单
        $currencyMenu->addChild('国家管理')
            ->setUri($this->linkGenerator->getCurdListPage(Country::class))
            ->setAttribute('icon', 'fas fa-globe');

        // 货币管理菜单
        $currencyMenu->addChild('货币列表')
            ->setUri($this->linkGenerator->getCurdListPage(Currency::class))
            ->setAttribute('icon', 'fas fa-coins');

        // 历史汇率管理菜单
        $currencyMenu->addChild('历史汇率')
            ->setUri($this->linkGenerator->getCurdListPage(CurrencyRateHistory::class))
            ->setAttribute('icon', 'fas fa-chart-line');
    }
}
