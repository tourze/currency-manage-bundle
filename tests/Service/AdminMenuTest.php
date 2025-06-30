<?php

namespace Tourze\CurrencyManageBundle\Test\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\Service\AdminMenu;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

/**
 * AdminMenu 服务测试
 */
class AdminMenuTest extends TestCase
{
    private LinkGeneratorInterface $linkGenerator;
    private AdminMenu $adminMenu;

    protected function setUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->adminMenu = new AdminMenu($this->linkGenerator);
    }

    public function testInvokeCreatesMenuItems(): void
    {
        // 创建根菜单项mock
        $rootMenuItem = $this->createMock(ItemInterface::class);
        $currencyMenuItem = $this->createMock(ItemInterface::class);
        
        // 设置期望的方法调用
        $rootMenuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('货币管理')
            ->willReturnOnConsecutiveCalls(null, $currencyMenuItem);
            
        $rootMenuItem->expects($this->once())
            ->method('addChild')
            ->with('货币管理')
            ->willReturn($currencyMenuItem);

        // 设置货币菜单的子项
        $countryMenuItem = $this->createMock(ItemInterface::class);
        $currencyListMenuItem = $this->createMock(ItemInterface::class);
        $historyMenuItem = $this->createMock(ItemInterface::class);

        $currencyMenuItem->expects($this->exactly(3))
            ->method('addChild')
            ->willReturnCallback(function ($name) use ($countryMenuItem, $currencyListMenuItem, $historyMenuItem) {
                return match ($name) {
                    '国家管理' => $countryMenuItem,
                    '货币列表' => $currencyListMenuItem,
                    '历史汇率' => $historyMenuItem,
                    default => $this->createMock(ItemInterface::class),
                };
            });

        // 设置URI和属性
        $countryMenuItem->expects($this->once())->method('setUri')->willReturnSelf();
        $countryMenuItem->expects($this->once())->method('setAttribute')->willReturnSelf();
        
        $currencyListMenuItem->expects($this->once())->method('setUri')->willReturnSelf();
        $currencyListMenuItem->expects($this->once())->method('setAttribute')->willReturnSelf();
        
        $historyMenuItem->expects($this->once())->method('setUri')->willReturnSelf();
        $historyMenuItem->expects($this->once())->method('setAttribute')->willReturnSelf();

        // 执行测试
        ($this->adminMenu)($rootMenuItem);
    }

    public function testInvokeWithExistingCurrencyMenu(): void
    {
        // 创建根菜单项mock
        $rootMenuItem = $this->createMock(ItemInterface::class);
        $currencyMenuItem = $this->createMock(ItemInterface::class);
        
        // 模拟已存在的货币管理菜单
        $rootMenuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('货币管理')
            ->willReturn($currencyMenuItem);
            
        $rootMenuItem->expects($this->never())
            ->method('addChild');

        // 设置子菜单项
        $countryMenuItem = $this->createMock(ItemInterface::class);
        $currencyListMenuItem = $this->createMock(ItemInterface::class);
        $historyMenuItem = $this->createMock(ItemInterface::class);

        $currencyMenuItem->expects($this->exactly(3))
            ->method('addChild')
            ->willReturnCallback(function ($name) use ($countryMenuItem, $currencyListMenuItem, $historyMenuItem) {
                return match ($name) {
                    '国家管理' => $countryMenuItem,
                    '货币列表' => $currencyListMenuItem,
                    '历史汇率' => $historyMenuItem,
                    default => $this->createMock(ItemInterface::class),
                };
            });

        // 设置URI和属性
        $countryMenuItem->expects($this->once())->method('setUri')->willReturnSelf();
        $countryMenuItem->expects($this->once())->method('setAttribute')->willReturnSelf();
        
        $currencyListMenuItem->expects($this->once())->method('setUri')->willReturnSelf();
        $currencyListMenuItem->expects($this->once())->method('setAttribute')->willReturnSelf();
        
        $historyMenuItem->expects($this->once())->method('setUri')->willReturnSelf();
        $historyMenuItem->expects($this->once())->method('setAttribute')->willReturnSelf();

        // 执行测试
        ($this->adminMenu)($rootMenuItem);
    }
} 