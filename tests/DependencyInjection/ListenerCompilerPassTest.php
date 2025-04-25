<?php

namespace Tourze\UserTrackBundle\Tests\DependencyInjection;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tourze\UserEventBundle\Event\UserInteractionEvent;
use Tourze\UserTrackBundle\DependencyInjection\ListenerCompilerPass;
use Tourze\UserTrackBundle\EventSubscriber\UserTrackListener;

class ListenerCompilerPassTest extends TestCase
{
    private ContainerBuilder|MockObject $container;
    private Definition|MockObject $definition;
    private ListenerCompilerPass $compilerPass;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->definition = $this->createMock(Definition::class);
        $this->compilerPass = new ListenerCompilerPass();

        // 设置findDefinition的返回值
        $this->container->method('findDefinition')
            ->with(UserTrackListener::class)
            ->willReturn($this->definition);
    }

    public function testProcess(): void
    {
        // 模拟工具特性的行为
        $reflection = new \ReflectionClass($this->compilerPass);
        $method = $reflection->getMethod('fetchUserInteractionEvents');
        $method->setAccessible(true);

        // 创建测试工具特性的替代方法
        $compilerPass = new class($method) extends ListenerCompilerPass {
            private \ReflectionMethod $method;

            public function __construct(\ReflectionMethod $method)
            {
                $this->method = $method;
            }

            public function fetchUserInteractionEvents(ContainerBuilder $container): array
            {
                return [UserInteractionEvent::class];
            }
        };

        // 期望添加标签
        $this->definition->expects($this->once())
            ->method('addTag')
            ->with('kernel.event_listener', [
                'event' => UserInteractionEvent::class,
                'priority' => -10,
            ]);

        // 执行测试
        $compilerPass->process($this->container);
    }
}
