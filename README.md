# Currency Manage Bundle

货币管理 Bundle，提供货币信息管理和汇率更新功能。

## 功能特性

- 货币实体管理（名称、代码、符号、汇率等）
- 自动汇率更新（通过定时任务）
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
$currency->setFlag('us'); // 国旗代码
$currency->setRateToCny(7.2);
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
# 手动更新汇率
php bin/console curreny-manage:update-rate

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

## API 接口

### 获取国旗图片

```http
GET /currency/flag/{code}
```

参数：
- `code`: 国旗代码（如：us, cn, eu）

返回：SVG 格式的国旗图片

示例：
```http
GET /currency/flag/us
GET /currency/flag/cn/1x1
```

## 测试

```bash
# 运行所有测试
./vendor/bin/phpunit packages/currency-manage-bundle/tests

# 运行特定测试
./vendor/bin/phpunit packages/currency-manage-bundle/tests/Service/FlagServiceTest.php

# 查看货币映射覆盖率
./vendor/bin/phpunit packages/currency-manage-bundle/tests/Service/CurrencyMappingCoverageTest.php
```

## 依赖

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- lipis/flag-icons 7.3+

## 许可证

MIT
