<?php

namespace Tourze\UserTrackBundle\Tests\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;
use Tourze\UserEventBundle\Event\UserInteractionEvent;
use Tourze\UserIDBundle\Model\SystemUser;
use Tourze\UserTrackBundle\Entity\TrackLog;
use Tourze\UserTrackBundle\Event\TrackContextInterface;
use Tourze\UserTrackBundle\EventSubscriber\UserTrackListener;

class UserTrackListenerTest extends TestCase
{
    private DoctrineService|MockObject $doctrineService;
    private LoggerInterface|MockObject $logger;
    private UserTrackListener $listener;

    protected function setUp(): void
    {
        $this->doctrineService = $this->createMock(DoctrineService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->listener = new UserTrackListener(
            $this->doctrineService,
            $this->logger
        );
    }

    public function testInvokeWithSystemUserReceiver(): void
    {
        // 创建发送者
        $sender = $this->createMock(UserInterface::class);
        $sender->method('getUserIdentifier')->willReturn('user123');

        // 创建接收者 (SystemUser)
        $receiver = $this->createMock(SystemUser::class);
        $receiver->method('getUserIdentifier')->willReturn('system');

        // 创建事件
        $event = $this->createMock(UserInteractionEvent::class);
        $event->method('getSender')->willReturn($sender);
        $event->method('getReceiver')->willReturn($receiver);
        $event->method('getMessage')->willReturn('test.message');

        // 设置期望
        $this->logger->expects($this->once())
            ->method('debug');

        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->with($this->callback(function (TrackLog $log) {
                return $log->getUserId() === 'user123'
                    && $log->getEvent() === 'test.message';
            }));

        // 执行测试
        ($this->listener)($event);
    }

    public function testInvokeWithTrackContextInterface(): void
    {
        // 创建发送者
        $sender = $this->createMock(UserInterface::class);
        $sender->method('getUserIdentifier')->willReturn('user123');

        // 创建接收者 (SystemUser)
        $receiver = $this->createMock(SystemUser::class);
        $receiver->method('getUserIdentifier')->willReturn('system');

        // 创建同时实现UserInteractionEvent和TrackContextInterface的模拟对象
        $trackingParams = ['key' => 'value'];

        // 使用正确的方式创建复合接口的模拟对象
        $event = $this->getMockBuilder(EventWithContext::class)
            ->getMock();
        $event->method('getSender')->willReturn($sender);
        $event->method('getReceiver')->willReturn($receiver);
        $event->method('getMessage')->willReturn('test.context.message');
        $event->method('getTrackingParams')->willReturn($trackingParams);

        // 设置期望
        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->with($this->callback(function (TrackLog $log) use ($trackingParams) {
                return $log->getUserId() === 'user123'
                    && $log->getEvent() === 'test.context.message'
                    && $log->getParams() === $trackingParams;
            }));

        // 执行测试
        ($this->listener)($event);
    }

    public function testInvokeWithNonSystemUserReceiver(): void
    {
        // 创建发送者
        $sender = $this->createMock(UserInterface::class);
        $sender->method('getUserIdentifier')->willReturn('user123');

        // 创建接收者 (非SystemUser)
        $receiver = $this->createMock(UserInterface::class);
        $receiver->method('getUserIdentifier')->willReturn('user456');

        // 创建事件
        $event = $this->createMock(UserInteractionEvent::class);
        $event->method('getSender')->willReturn($sender);
        $event->method('getReceiver')->willReturn($receiver);
        $event->method('getMessage')->willReturn('test.message');

        // 设置期望：不会调用asyncInsert
        $this->doctrineService->expects($this->never())
            ->method('asyncInsert');

        // 执行测试
        ($this->listener)($event);
    }

    public function testInvokeWithExceptionHandling(): void
    {
        // 创建发送者
        $sender = $this->createMock(UserInterface::class);
        $sender->method('getUserIdentifier')->willReturn('user123');

        // 创建接收者 (SystemUser)
        $receiver = $this->createMock(SystemUser::class);
        $receiver->method('getUserIdentifier')->willReturn('system');

        // 创建事件
        $event = $this->createMock(UserInteractionEvent::class);
        $event->method('getSender')->willReturn($sender);
        $event->method('getReceiver')->willReturn($receiver);
        $event->method('getMessage')->willReturn('test.message');

        // 设置异常
        $exception = new \Exception('Test exception');
        $this->doctrineService->method('asyncInsert')
            ->willThrowException($exception);

        // 设置期望
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo('保存客户行为信息时发生异常'),
                $this->callback(function ($context) use ($exception) {
                    return isset($context['exception']) && $context['exception'] === $exception;
                })
            );

        // 执行测试
        ($this->listener)($event);
    }
}

/**
 * 辅助测试的复合接口实现类
 */
abstract class EventWithContext extends UserInteractionEvent implements TrackContextInterface
{
}
