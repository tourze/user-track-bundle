<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\EventSubscriber;

use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

/**
 * 测试用的 UserInteractionEvent 实现
 */
final class TestUserInteractionEvent extends UserInteractionEvent
{
    public function __construct(
        private readonly UserInterface $sender,
        private readonly UserInterface $receiver,
        private readonly string $message,
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
}
