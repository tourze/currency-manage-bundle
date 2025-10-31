<?php

namespace Tourze\CurrencyManageBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CurrencyManageBundle\Service\AdminMenu;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * AdminMenu 服务测试
 *
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    protected function onSetUp(): void
    {
        // 使用简单的测试替身代替Mock
        $linkGenerator = new class implements LinkGeneratorInterface {
            /** @var array<int, string> */
            private array $calls = [];

            public function getCurdListPage(string $entityClass): string
            {
                $this->calls[] = $entityClass;

                return '/admin/' . strtolower(basename(str_replace('\\', '/', $entityClass)));
            }

            public function extractEntityFqcn(string $routeName): ?string
            {
                // 测试中不需要实现，返回null即可
                return null;
            }

            public function setDashboard(string $dashboardControllerFqcn): void
            {
                // 测试中不需要实现，空方法即可
            }

            /** @return array<int, string> */
            public function getCalls(): array
            {
                return $this->calls;
            }
        };

        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);
        $adminMenu = self::getContainer()->get(AdminMenu::class);
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
        $this->adminMenu = $adminMenu;
    }

    public function testInvoke(): void
    {
        // 使用简单的SPY模式测试替身，只记录调用信息
        $calls = [];

        // 创建一个最小化的测试替身
        $menuItem = $this->createMock(ItemInterface::class);
        $currencyMenu = $this->createMock(ItemInterface::class);

        // 记录调用而不是验证
        $getChildCallCount = 0;
        $menuItem->method('getChild')
            ->with('货币管理')
            ->willReturnCallback(function () use (&$getChildCallCount, $currencyMenu) {
                ++$getChildCallCount;
                // 第一次返回null触发创建，第二次返回菜单对象
                return $getChildCallCount === 1 ? null : $currencyMenu;
            })
        ;

        $menuItem->method('addChild')
            ->with('货币管理')
            ->willReturnCallback(function ($name) use (&$calls, $currencyMenu) {
                $calls[] = ['addChild', $name];

                return $currencyMenu;
            })
        ;

        // 记录子菜单的添加
        $currencyMenu->method('addChild')
            ->willReturnCallback(function ($name) use (&$calls, $currencyMenu) {
                $calls[] = ['addChildToMenu', $name];

                return $currencyMenu;
            })
        ;

        $currencyMenu->method('setUri')
            ->willReturnSelf()
        ;

        $currencyMenu->method('setAttribute')
            ->willReturnSelf()
        ;

        // 执行测试
        ($this->adminMenu)($menuItem);

        // 验证调用记录 - 更关注行为而非细节
        $this->assertContains(['addChild', '货币管理'], $calls, '应该创建货币管理菜单');
        $this->assertContains(['addChildToMenu', '国家管理'], $calls, '应该添加国家管理菜单');
        $this->assertContains(['addChildToMenu', '货币列表'], $calls, '应该添加货币列表菜单');
        $this->assertContains(['addChildToMenu', '历史汇率'], $calls, '应该添加历史汇率菜单');
    }
}
