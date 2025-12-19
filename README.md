# UserTrackBundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/user-track-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/user-track-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/user-track-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/user-track-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/user-track-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/user-track-bundle)
[![License](https://img.shields.io/packagist/l/tourze/user-track-bundle.svg?style=flat-square)](LICENSE)
[![Code Coverage](https://codecov.io/gh/tourze/user-track-bundle/branch/main/graph/badge.svg)](https://codecov.io/gh/tourze/user-track-bundle)

A Symfony bundle for tracking and recording user behavior across your application. 
Provides automatic event tracking, manual tracking via JSON-RPC, and built-in log rotation.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Advanced Usage](#advanced-usage)
- [Configuration](#configuration)
- [JSON-RPC API Reference](#json-rpc-api-reference)
- [Use Cases](#use-cases)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Automatic Event Tracking**: Automatically captures `UserInteractionEvent` events
- **Asynchronous Storage**: Uses async insertion to avoid impacting main business performance
- **JSON-RPC Interface**: Manual behavior tracking through RPC endpoint
- **Automatic Log Cleanup**: Built-in scheduled cleanup of expired logs (configurable retention period)
- **Customer Radar Support**: Provides data foundation for CRM customer radar functionality
- **Flexible Event Parameters**: Support for custom tracking parameters through interfaces
- **Distributed Lock Support**: Prevents duplicate submissions in distributed environments

## Requirements

- PHP 8.1+
- Symfony 6.4+
- tourze/user-event-bundle
- tourze/doctrine-snowflake-bundle
- tourze/doctrine-ip-bundle

## Installation

```bash
composer require tourze/user-track-bundle
```

## Quick Start

### 1. Register the Bundle

```php
// config/bundles.php
return [
    // ...
    Tourze\UserTrackBundle\UserTrackBundle::class => ['all' => true],
];
```

### 2. Automatic Event Tracking

Any `UserInteractionEvent` dispatched in your application will be automatically tracked:

```php
use Tourze\UserEventBundle\Event\UserInteractionEvent;

// This event will be automatically recorded
$event = new UserInteractionEvent($sender, $systemUser, 'user.login');
$eventDispatcher->dispatch($event);
```

### 3. Manual Tracking via JSON-RPC

```php
// Frontend JavaScript
const response = await jsonRpcClient.call('SubmitCrmTrackLog', {
    event: 'page.view',
    params: {
        page: '/products/123',
        referrer: 'google.com',
        duration: 30
    }
});
// Response: { time: 1234567890 }

// Backend PHP
$result = $jsonRpcClient->call('SubmitCrmTrackLog', [
    'event' => 'order.completed',
    'params' => [
        'order_id' => '12345',
        'amount' => 99.99
    ]
]);
```

## Advanced Usage

### Custom Tracking Parameters

Implement `TrackContextInterface` in your events to provide additional tracking parameters:

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

### Extending RPC Response

Listen to `TrackLogReportEvent` to modify the RPC response:

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

## Configuration

### Environment Variables

```bash
# .env
# Number of days to keep track logs (default: 30)
CLEAN_TRACK_LOG_DAY_NUM=90
```

### Automatic Cleanup

Logs are automatically cleaned up daily at 2:20 AM. The retention period can be configured 
via the `CLEAN_TRACK_LOG_DAY_NUM` environment variable.

## JSON-RPC API Reference

### SubmitCrmTrackLog

Submit a user behavior tracking log.

**Parameters:**
- `event` (string, required): Event name (e.g., "page.view", "button.click")
- `params` (array, optional): Event parameters as key-value pairs

**Response:**
```json
{
    "time": 1234567890
}
```

**Authentication:** Requires authenticated user (`IS_AUTHENTICATED`)

## Use Cases

- **User Behavior Analytics**: Track page views, clicks, feature usage
- **Customer Radar**: Real-time customer activity tracking for CRM
- **Marketing Automation**: Trigger marketing campaigns based on user behavior
- **Security Auditing**: Record audit logs for sensitive operations
- **Product Optimization**: Analyze user behavior data to improve features

## Testing

Run tests from the monorepo root:

```bash
# Run all tests
./vendor/bin/phpunit packages/user-track-bundle/tests

# Run specific test
./vendor/bin/phpunit packages/user-track-bundle/tests/Entity/TrackLogTest.php
```

### Test Coverage

- [x] Entity tests - Complete coverage of TrackLog entity
- [x] Event tests - TrackLogReportEvent and TrackContext interface
- [x] Event Subscriber tests - UserTrackListener behavior
- [x] RPC Procedure tests - SubmitCrmTrackLog endpoint
- [x] DI Extension tests - Service registration and configuration
- [x] Bundle tests - Bundle initialization

## Security

### Authentication

All JSON-RPC endpoints require authenticated users. User tracking data is stored 
with IP address information for security auditing purposes.

### Data Protection

- User tracking data is automatically cleaned up based on configured retention period
- IP addresses are stored for security purposes and follow the same retention policy
- All user input is validated before storage

### Best Practices

- Review tracking parameters to avoid storing sensitive information
- Use appropriate retention periods for compliance requirements
- Monitor tracking data for unusual patterns that might indicate security issues

## Contributing

Contributions are welcome! Please ensure:
- All tests pass
- Code follows PSR-12 standards
- New features include tests
- Documentation is updated

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
