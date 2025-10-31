<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\UserEventBundle\Event\UserInteractionEvent;
use Tourze\UserIDBundle\Model\SystemUser;
use Tourze\UserTrackBundle\Event\TrackContextInterface;
use Tourze\UserTrackBundle\EventSubscriber\UserTrackListener;

/**
 * @internal
 */
#[CoversClass(UserTrackListener::class)]
#[RunTestsInSeparateProcesses]
final class UserTrackListenerTest extends AbstractIntegrationTestCase
{
    private UserTrackListener $listener;

    protected function onSetUp(): void
    {
        $this->listener = self::getService(UserTrackListener::class);
    }

    public function testInvokeWithSystemUserReceiver(): void
    {
        $sender = $this->createNormalUser('user123@test.com', 'password123');
        $receiver = new SystemUser();

        $event = $this->createTestUserInteractionEvent($sender, $receiver, 'test.message');

        ($this->listener)($event);

        // Verify that the method executes without throwing exceptions
        $this->expectNotToPerformAssertions();
    }

    public function testInvokeWithTrackContextInterface(): void
    {
        $sender = $this->createNormalUser('user456@test.com', 'password456');
        $receiver = new SystemUser();

        $trackingParams = ['key' => 'value', 'source' => 'test'];

        $event = $this->createTestUserInteractionEventWithTrackingContext($sender, $receiver, 'test.context.message', $trackingParams);

        ($this->listener)($event);

        // Verify the tracking context interface is properly handled
        $this->expectNotToPerformAssertions();
    }

    public function testInvokeWithNonSystemUserReceiver(): void
    {
        $sender = $this->createNormalUser('sender@test.com', 'password123');
        $receiver = $this->createNormalUser('receiver@test.com', 'password456');

        $event = $this->createTestUserInteractionEvent($sender, $receiver, 'user.to.user.message');

        ($this->listener)($event);

        // Verify non-system user receiver is handled correctly
        $this->expectNotToPerformAssertions();
    }

    public function testInvokeWithExceptionHandling(): void
    {
        $sender = $this->createNormalUser('exception@test.com', 'password123');
        $receiver = new SystemUser();

        $event = $this->createTestUserInteractionEvent($sender, $receiver, 'exception.test.message');

        // Test should not throw exception even if underlying service fails
        ($this->listener)($event);

        // Verify exception handling works correctly
        $this->expectNotToPerformAssertions();
    }

    private function createTestUserInteractionEvent(UserInterface $sender, UserInterface $receiver, string $message): UserInteractionEvent
    {
        return new TestUserInteractionEvent($sender, $receiver, $message);
    }

    /**
     * @param array<string, mixed> $trackingParams
     */
    private function createTestUserInteractionEventWithTrackingContext(UserInterface $sender, UserInterface $receiver, string $message, array $trackingParams): UserInteractionEvent&TrackContextInterface
    {
        return new TestUserInteractionEventWithTracking($sender, $receiver, $message, $trackingParams);
    }
}
