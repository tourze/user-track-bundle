# UserTrackBundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/user-track-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/user-track-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/user-track-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/user-track-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/user-track-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/user-track-bundle)
[![License](https://img.shields.io/packagist/l/tourze/user-track-bundle.svg?style=flat-square)](LICENSE)
[![Code Coverage](https://codecov.io/gh/tourze/user-track-bundle/branch/main/graph/badge.svg)](https://codecov.io/gh/tourze/user-track-bundle)

一个用于跟踪和记录应用程序中用户行为的 Symfony Bundle。
提供自动事件跟踪、通过 JSON-RPC 手动跟踪以及内置日志轮转功能。

## 目录

- [功能特性](#功能特性)
- [要求](#要求)
- [安装](#安装)
- [快速开始](#快速开始)
- [高级用法](#高级用法)
- [配置](#配置)
- [JSON-RPC API 参考](#json-rpc-api-参考)
- [使用场景](#使用场景)
- [测试](#测试)
- [安全](#安全)
- [贡献](#贡献)
- [许可证](#许可证)

## 功能特性

- **自动事件跟踪**：自动捕获 `UserInteractionEvent` 事件
- **异步存储**：使用异步插入避免影响主业务性能
- **JSON-RPC 接口**：通过 RPC 端点进行手动行为跟踪
- **自动日志清理**：内置定时清理过期日志（可配置保留期）
- **客户雷达支持**：为 CRM 客户雷达功能提供数据基础
- **灵活的事件参数**：通过接口支持自定义跟踪参数
- **分布式锁支持**：在分布式环境中防止重复提交

## 要求

- PHP 8.1+
- Symfony 6.4+
- tourze/user-event-bundle
- tourze/doctrine-snowflake-bundle
- tourze/doctrine-ip-bundle

## 安装

```bash
composer require tourze/user-track-bundle
```

## 快速开始

### 1. 注册 Bundle

```php
// config/bundles.php
return [
    // ...
    Tourze\UserTrackBundle\UserTrackBundle::class => ['all' => true],
];
```

### 2. 自动事件跟踪

应用程序中分发的任何 `UserInteractionEvent` 都会被自动跟踪：

```php
use Tourze\UserEventBundle\Event\UserInteractionEvent;

// 这个事件将被自动记录
$event = new UserInteractionEvent($sender, $systemUser, 'user.login');
$eventDispatcher->dispatch($event);
```

### 3. 通过 JSON-RPC 手动跟踪

```php
// 前端 JavaScript
const response = await jsonRpcClient.call('SubmitCrmTrackLog', {
    event: 'page.view',
    params: {
        page: '/products/123',
        referrer: 'google.com',
        duration: 30
    }
});
// 响应: { time: 1234567890 }

// 后端 PHP
$result = $jsonRpcClient->call('SubmitCrmTrackLog', [
    'event' => 'order.completed',
    'params' => [
        'order_id' => '12345',
        'amount' => 99.99
    ]
]);
```

## 高级用法

### 自定义跟踪参数

在你的事件中实现 `TrackContextInterface` 以提供额外的跟踪参数：

```php
use Tourze\UserEventBundle\Event\UserInteractionEvent;
use Tourze\UserTrackBundle\Event\TrackContextInterface;

class ProductViewEvent extends UserInteractionEvent implements TrackContextInterface
{
    /** @var array<string, mixed> */
    private array $trackParams = [];

    public function __construct($user, $product)
    {
        parent::__construct($user, $systemUser, 'product.view');

        $this->setTrackingParams([
            'product_id' => $product->getId(),
            'category' => $product->getCategory(),
            'price' => $product->getPrice()
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getTrackingParams(): array
    {
        return $this->trackParams;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function setTrackingParams(array $params): void
    {
        $this->trackParams = $params;
    }
}
```

### 扩展 RPC 响应

监听 `TrackLogReportEvent` 以修改 RPC 响应：

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tourze\UserTrackBundle\Event\TrackLogReportEvent;

class TrackLogResponseEnricher implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            TrackLogReportEvent::class => 'onTrackLogReport',
        ];
    }
    
    public function onTrackLogReport(TrackLogReportEvent $event)
    {
        $result = $event->getResult();
        $result['tracking_id'] = $event->getTrackLog()->getId();
        $event->setResult($result);
    }
}
```

## 配置

### 环境变量

```bash
# .env
# 保留跟踪日志的天数（默认：30）
CLEAN_TRACK_LOG_DAY_NUM=90
```

### 自动清理

日志每天凌晨 2:20 自动清理。保留期可以通过 `CLEAN_TRACK_LOG_DAY_NUM` 环境变量配置。

## JSON-RPC API 参考

### SubmitCrmTrackLog

提交用户行为跟踪日志。

**参数：**
- `event` (string, 必填)：事件名称（例如："page.view", "button.click"）
- `params` (array, 可选)：事件参数，键值对

**响应：**
```json
{
    "time": 1234567890
}
```

**认证：** 需要认证用户（`IS_AUTHENTICATED`）

## 使用场景

- **用户行为分析**：跟踪页面浏览、点击、功能使用
- **客户雷达**：CRM 的实时客户活动跟踪
- **营销自动化**：基于用户行为触发营销活动
- **安全审计**：记录敏感操作的审计日志
- **产品优化**：分析用户行为数据以改进功能

## 测试

从 monorepo 根目录运行测试：

```bash
# 运行所有测试
./vendor/bin/phpunit packages/user-track-bundle/tests

# 运行特定测试
./vendor/bin/phpunit packages/user-track-bundle/tests/Entity/TrackLogTest.php
```

### 测试覆盖率

- [x] 实体测试 - TrackLog 实体的完整覆盖
- [x] 事件测试 - TrackLogReportEvent 和 TrackContext 接口
- [x] 事件订阅器测试 - UserTrackListener 行为
- [x] RPC 过程测试 - SubmitCrmTrackLog 端点
- [x] DI 扩展测试 - 服务注册和配置
- [x] Bundle 测试 - Bundle 初始化

## 安全

### 认证

所有 JSON-RPC 端点都需要认证用户。用户跟踪数据存储时包含 IP 地址信息用于安全审计。

### 数据保护

- 用户跟踪数据根据配置的保留期自动清理
- IP 地址存储用于安全目的，遵循相同的保留策略
- 所有用户输入在存储前经过验证

### 最佳实践

- 检查跟踪参数以避免存储敏感信息
- 使用适当的保留期满足合规要求
- 监控跟踪数据中可能表明安全问题的异常模式

## 贡献

欢迎贡献！请确保：
- 所有测试通过
- 代码遵循 PSR-12 标准
- 新功能包含测试
- 更新文档

## 许可证

MIT 许可证（MIT）。详情请查看[许可文件](LICENSE)。
