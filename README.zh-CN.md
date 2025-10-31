# 货币管理包 (Currency Management Bundle)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/currency-manage-bundle)](https://packagist.org/packages/tourze/currency-manage-bundle)
[![License](https://img.shields.io/packagist/l/tourze/currency-manage-bundle)](https://packagist.org/packages/tourze/currency-manage-bundle)

[![CI Status](https://img.shields.io/github/actions/workflow/status/tourze/currency-manage-bundle/ci.yml?branch=main)](https://github.com/tourze/currency-manage-bundle/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/currency-manage-bundle)](https://codecov.io/gh/tourze/currency-manage-bundle)

[English](README.md) | [中文](README.zh-CN.md)

货币管理 Bundle，提供货币信息管理和汇率更新功能。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [配置](#配置)
  - [1. 注册 Bundle](#1-注册-bundle)
  - [2. 数据库迁移](#2-数据库迁移)
  - [3. 路由配置](#3-路由配置)
- [使用方法](#使用方法)
  - [货币实体](#货币实体)
  - [历史汇率实体](#历史汇率实体)
  - [历史汇率查询](#历史汇率查询)
  - [国旗服务](#国旗服务)
  - [汇率更新命令](#汇率更新命令)
- [国旗映射](#国旗映射)
- [国旗图片接口](#国旗图片接口)
- [EasyAdmin 管理界面](#easyadmin-管理界面)
- [Advanced Usage](#advanced-usage)
- [测试](#测试)
- [数据库表结构](#数据库表结构)
- [性能优化](#性能优化)
- [数据清理](#数据清理)
- [依赖](#依赖)
- [许可证](#许可证)

## 功能特性

- 货币实体管理（名称、代码、符号、汇率等）
- 自动汇率更新（通过定时任务）
- **历史汇率记录和查询**
- 国旗图标支持（基于 lipis/flag-icons）
- EasyAdmin 后台管理界面
- 完整的单元测试覆盖

## 安装

```bash
composer require tourze/currency-manage-bundle
```

## 配置

### 1. 注册 Bundle

在 `config/bundles.php` 中添加：

```php
return [
    // ...
    Tourze\CurrencyManageBundle\CurrencyManageBundle::class => ['all' => true],
];
```

### 2. 数据库迁移

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### 3. 路由配置

Bundle 使用 `AttributeControllerLoader` 自动注册路由，包括：

- `/currency/flag/{code}` - 获取 4x3 比例的国旗图片
- `/currency/flag/{code}/1x1` - 获取 1x1 比例的国旗图片

## 使用方法

### 货币实体

```php
use Tourze\CurrencyManageBundle\Entity\Currency;

$currency = new Currency();
$currency->setName('美元');
$currency->setCode('USD');
$currency->setSymbol('$');
$currency->setRateToCny(7.2);
```

### 历史汇率实体

```php
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;

$history = new CurrencyRateHistory();
$history->setCurrencyCode('USD');
$history->setCurrencyName('美元');
$history->setCurrencySymbol('$');
$history->setFlag('us');
$history->setRateToCny(7.2);
$history->setRateDate(new \DateTime('2025-01-01'));
```

### 历史汇率查询

```php
use Tourze\CurrencyManageBundle\Repository\CurrencyRateHistoryRepository;

// 根据货币代码查询历史汇率
$histories = $repository->findByCurrencyCode('USD', 30);

// 根据日期范围查询
$startDate = new \DateTime('2025-01-01');
$endDate = new \DateTime('2025-01-31');
$histories = $repository->findByDateRange($startDate, $endDate, 'USD');

// 获取最新历史汇率
$latest = $repository->findLatestByCurrency('USD');

// 获取统计信息
$stats = $repository->getStatistics();
```

### 国旗服务

```php
use Tourze\CurrencyManageBundle\Service\FlagService;

$flagService = new FlagService();

// 获取国旗文件路径
$flagPath = $flagService->getFlagPath('us', '4x3');

// 检查国旗是否存在
$exists = $flagService->flagExists('us');

// 根据货币代码获取国旗代码
$flagCode = $flagService->getFlagCodeFromCurrency('USD'); // 返回 'us'

// 获取所有可用的国旗代码
$availableFlags = $flagService->getAvailableFlags();
```

### 汇率更新命令

```bash
# 手动更新汇率（同时记录历史数据）
php bin/console currency-manage:update-rate

# 定时任务会在每天 8:40 自动执行
```

## 国旗映射

Bundle 支持 176+ 种货币的国旗映射，覆盖率达到 61.75%，包括：

### 主要货币

- CNY → cn (中国)
- USD → us (美国)  
- EUR → eu (欧盟)
- JPY → jp (日本)
- GBP → gb (英国)
- 等等...

### 特殊处理

- 历史货币映射到对应的国家/地区
- 货币联盟使用代表性国家的国旗
- 贵金属等特殊货币返回 null

## 国旗图片接口

### 获取国旗图片

```text
GET /currency/flag/{code}
```

参数：

- `code`: 国旗代码（如：us, cn, eu）

返回：SVG 格式的国旗图片

示例：

```text
GET /currency/flag/us
GET /currency/flag/cn/1x1
```

## EasyAdmin 管理界面

Bundle 提供了完整的 EasyAdmin 管理界面：

1. **货币管理** - 管理货币基本信息和当前汇率
2. **历史汇率** - 查看和管理历史汇率记录（只读）

管理界面支持：

- 搜索和过滤
- 分页显示
- 详情查看
- 数据导出

## Advanced Usage

### 自定义汇率更新服务

```php
use Tourze\CurrencyManageBundle\Service\CurrencyRateService;

class CustomCurrencyService
{
    public function __construct(
        private CurrencyRateService $currencyRateService
    ) {}

    public function updateRatesFromCustomSource(): array
    {
        // 自定义汇率更新逻辑
        $exchangeData = $this->fetchFromCustomAPI();
        
        $results = [];
        foreach ($exchangeData as $code => $rate) {
            $result = $this->currencyRateService->updateCurrencyRate(
                $code,
                $this->getCurrencyName($code),
                $rate,
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            );
            $results[] = $result;
        }
        
        return $results;
    }
}
```

### 扩展国旗映射

```php
use Tourze\CurrencyManageBundle\Service\FlagService;

class ExtendedFlagService extends FlagService
{
    protected function getCustomMappings(): array
    {
        return [
            'BTC' => 'bitcoin',  // 自定义映射
            'ETH' => 'ethereum',
            // 添加更多自定义映射...
        ];
    }
}
```

### 历史汇率分析

```php
use Tourze\CurrencyManageBundle\Repository\CurrencyRateHistoryRepository;

class CurrencyAnalyzer
{
    public function __construct(
        private CurrencyRateHistoryRepository $historyRepository
    ) {}

    public function calculateVolatility(string $currencyCode, int $days = 30): float
    {
        $histories = $this->historyRepository->findByCurrencyCode($currencyCode, $days);
        
        if (count($histories) < 2) {
            return 0.0;
        }
        
        $rates = array_map(fn($h) => $h->getRateToCny(), $histories);
        $mean = array_sum($rates) / count($rates);
        
        $variance = array_sum(array_map(fn($r) => pow($r - $mean, 2), $rates)) / count($rates);
        
        return sqrt($variance);
    }
}
```

### 事件监听

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tourze\CurrencyManageBundle\Event\CurrencyRateUpdatedEvent;

class CurrencyEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CurrencyRateUpdatedEvent::class => 'onCurrencyRateUpdated',
        ];
    }

    public function onCurrencyRateUpdated(CurrencyRateUpdatedEvent $event): void
    {
        // 汇率更新后的自定义处理逻辑
        $currency = $event->getCurrency();
        $oldRate = $event->getOldRate();
        $newRate = $event->getNewRate();
        
        // 例如：发送通知、更新缓存等
    }
}
```

## 测试

```bash
# 运行所有测试
./vendor/bin/phpunit packages/currency-manage-bundle/tests

# 运行特定测试
./vendor/bin/phpunit packages/currency-manage-bundle/tests/Service/FlagServiceTest.php

# 查看货币映射覆盖率
./vendor/bin/phpunit packages/currency-manage-bundle/tests/Service/CurrencyMappingCoverageTest.php

# 测试历史汇率功能
./vendor/bin/phpunit packages/currency-manage-bundle/tests/Entity/CurrencyRateHistoryTest.php
./vendor/bin/phpunit packages/currency-manage-bundle/tests/Repository/CurrencyRateHistoryRepositoryTest.php
```

## 数据库表结构

### 货币表 (starhome_currency)

- `id` - 主键
- `name` - 货币名称  
- `code` - 货币代码
- `flags` - 货币符号
- `flag` - 国旗代码
- `rateToCny` - 对人民币汇率
- `rateUpdateDate` - 汇率更新时间

### 历史汇率表 (starhome_currency_rate_history)

- `id` - 主键
- `currency_code` - 货币代码
- `currency_name` - 货币名称
- `currency_symbol` - 货币符号
- `flag` - 国旗代码
- `rateToCny` - 对人民币汇率
- `rate_date` - 汇率日期
- `createdAt` - 记录创建时间

索引：

- `idx_currency_code` - 货币代码索引
- `idx_rate_date` - 汇率日期索引
- `idx_currency_date` - 货币代码+日期复合索引

## 性能优化

1. **数据库索引** - 为常用查询字段添加索引
2. **缓存策略** - 国旗图片设置缓存头
3. **批量操作** - 汇率更新使用批量提交

## 数据清理

为避免历史数据无限增长，可以定期清理旧数据：

```php
// 删除一年前的历史记录
$cutoffDate = new \DateTime('-1 year');
$deletedCount = $repository->deleteBeforeDate($cutoffDate);
```

## 依赖

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- lipis/flag-icons 7.3+

## 贡献

请参阅 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详情。

## 许可证

MIT License (MIT)。请参阅 [License File](LICENSE) 获取更多信息。
