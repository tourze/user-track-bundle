<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\EventSubscriber;

use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserEventBundle\Event\UserInteractionEvent;
use Tourze\UserTrackBundle\Event\TrackContextInterface;

/**
 * 带有 TrackContextInterface 的测试用 UserInteractionEvent 实现
 */
final class TestUserInteractionEventWithTracking extends UserInteractionEvent implements TrackContextInterface
{
    /**
     * @param array<string, mixed> $trackingParams
     */
    public function __construct(
        private readonly UserInterface $sender,
        private readonly UserInterface $receiver,
        private readonly string $message,
        private array $trackingParams,
    ) {
    }

    public function getSender(): UserInterface
    {
        return $this->sender;
    }

    public function getReceiver(): UserInterface
    {
        return $this->receiver;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTrackingParams(): array
    {
        return $this->trackingParams;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function setTrackingParams(array $params): void
    {
        $this->trackingParams = $params;
    }
}
