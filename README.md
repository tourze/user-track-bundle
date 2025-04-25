# UserTrackBundle

[English](README.md) | [中文](README.zh-CN.md)

User behavior tracking bundle for recording various user operations in the system.

## Features

- Automatic recording of user interaction events
- Support for asynchronous log storage
- Provides RPC interface for manual behavior recording
- Support for automatic cleaning of expired logs

## Installation

```bash
composer require tourze/user-track-bundle
```

## Testing

Run tests with the following command:

```bash
./vendor/bin/phpunit packages/user-track-bundle/tests
```

## Test Plan Status

- [x] Entity tests - Completed
- [x] Event tests - Completed
- [x] Event Subscriber tests - Completed
- [x] RPC Procedure tests - Completed
- [x] Dependency Injection Extension tests - Completed
- [x] Bundle tests - Completed

## Usage

1. Register the Bundle

```php
// config/bundles.php
return [
    // ...
    Tourze\UserTrackBundle\UserTrackBundle::class => ['all' => true],
];
```

2. Record user behavior by listening to UserInteractionEvent

3. Manually record user behavior via RPC interface

```php
// Using JSON-RPC call
$result = $client->call('SubmitCrmTrackLog', [
    'event' => 'user.login',
    'params' => ['ip' => $ipAddress]
]);
```
