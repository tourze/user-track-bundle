# UserTrackBundle

[English](README.md) | [中文](README.zh-CN.md)

用户行为轨迹跟踪包，用于记录用户在系统中的各种操作行为。

## 功能

- 自动记录用户交互事件
- 支持异步日志存储
- 提供RPC接口进行手动行为记录
- 支持自动清理过期日志

## 安装

```bash
composer require tourze/user-track-bundle
```

## 测试

执行以下命令运行测试：

```bash
./vendor/bin/phpunit packages/user-track-bundle/tests
```

## 测试计划状态

- [x] 实体测试 - 完成
- [x] 事件测试 - 完成
- [x] 事件订阅器测试 - 完成
- [x] RPC过程测试 - 完成
- [x] 依赖注入扩展测试 - 完成
- [x] Bundle测试 - 完成

## 使用

1. 注册Bundle

```php
// config/bundles.php
return [
    // ...
    Tourze\UserTrackBundle\UserTrackBundle::class => ['all' => true],
];
```

2. 通过监听UserInteractionEvent记录用户行为

3. 通过RPC接口手动记录用户行为

```php
// 使用JSON-RPC调用
$result = $client->call('SubmitCrmTrackLog', [
    'event' => 'user.login',
    'params' => ['ip' => $ipAddress]
]);
```
