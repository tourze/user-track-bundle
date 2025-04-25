<?php

namespace Tourze\UserTrackBundle\Tests\Procedure;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\UserTrackBundle\Event\TrackLogReportEvent;
use Tourze\UserTrackBundle\Procedure\SubmitCrmTrackLog;

class SubmitCrmTrackLogTest extends TestCase
{
    private DoctrineService|MockObject $doctrineService;
    private Security|MockObject $security;
    private EventDispatcherInterface|MockObject $eventDispatcher;
    private SubmitCrmTrackLog $procedure;

    protected function setUp(): void
    {
        $this->doctrineService = $this->createMock(DoctrineService::class);
        $this->security = $this->createMock(Security::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->procedure = new SubmitCrmTrackLog(
            $this->doctrineService,
            $this->security,
            $this->eventDispatcher
        );
    }

    public function testExecute(): void
    {
        // 设置参数
        $this->procedure->event = 'test.event';
        $this->procedure->params = ['key' => 'value'];

        // 设置用户
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('user123');
        $this->security->method('getUser')->willReturn($user);

        // 捕获事件分发
        $capturedEvent = null;
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (TrackLogReportEvent $event) use (&$capturedEvent) {
                $capturedEvent = $event;
                return $event->getEvent() === 'test.event'
                    && $event->getParams() === ['key' => 'value'];
            }))
            ->willReturnArgument(0);

        // 验证异步插入
        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->with($this->callback(function ($log) {
                return $log->getEvent() === 'test.event'
                    && $log->getParams() === ['key' => 'value']
                    && $log->getUserId() === 'user123';
            }));

        // 执行测试
        $result = $this->procedure->execute();

        // 验证结果
        $this->assertIsArray($result);
        $this->assertArrayHasKey('time', $result);
    }

    public function testGetLockResource(): void
    {
        // 创建方法的反射
        $reflectionMethod = new \ReflectionMethod(SubmitCrmTrackLog::class, 'getLockResource');
        $reflectionMethod->setAccessible(true);

        // 创建用户
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('user123');
        $this->security->method('getUser')->willReturn($user);

        // 创建参数
        $params = $this->createMock(JsonRpcParams::class);
        $params->method('get')->with('event')->willReturn('test.event');

        // 调用方法
        $result = $reflectionMethod->invoke($this->procedure, $params);

        // 验证结果
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('user123-SubmitCrmTrackLog-test.event', $result[0]);
    }

    public function testGetLockResourceWithoutUser(): void
    {
        // 创建方法的反射
        $reflectionMethod = new \ReflectionMethod(SubmitCrmTrackLog::class, 'getLockResource');
        $reflectionMethod->setAccessible(true);

        // 没有用户
        $this->security->method('getUser')->willReturn(null);

        // 创建参数
        $params = $this->createMock(JsonRpcParams::class);

        // 调用方法
        $result = $reflectionMethod->invoke($this->procedure, $params);

        // 验证结果
        $this->assertNull($result);
    }
}
