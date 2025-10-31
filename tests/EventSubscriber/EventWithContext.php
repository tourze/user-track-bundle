<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\EventSubscriber;

use Tourze\UserEventBundle\Event\UserInteractionEvent;
use Tourze\UserTrackBundle\Event\TrackContextInterface;

/**
 * 辅助测试的复合接口实现类
 */
abstract class EventWithContext extends UserInteractionEvent implements TrackContextInterface
{
}
