<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tourze\UserEventBundle\Event\UserInteractionEvent;
use Tourze\UserTrackBundle\DependencyInjection\ListenerCompilerPass;
use Tourze\UserTrackBundle\EventSubscriber\UserTrackListener;

/**
 * @internal
 */
#[CoversClass(ListenerCompilerPass::class)]
final class ListenerCompilerPassTest extends TestCase
{
    private ContainerBuilder $container;

    private Definition $definition;

    protected function setUp(): void
    {
        parent::setUp();

        // 使用真实的 ContainerBuilder
        $this->container = new ContainerBuilder();

        // 使用真实的 Definition
        $this->definition = new Definition();

        // 注册定义到容器
        $this->container->setDefinition(UserTrackListener::class, $this->definition);
    }

    public function testProcess(): void
    {
        // 创建测试工具特性的替代方法
        $compilerPass = new class extends ListenerCompilerPass {
            public function fetchUserInteractionEvents(ContainerBuilder $container): iterable
            {
                return [UserInteractionEvent::class];
            }
        };

        // 执行测试
        $compilerPass->process($this->container);

        // 验证标签是否被添加
        $tags = $this->definition->getTags();
        $this->assertArrayHasKey('kernel.event_listener', $tags);

        $eventListenerTags = $tags['kernel.event_listener'];
        $this->assertIsArray($eventListenerTags);
        $this->assertNotEmpty($eventListenerTags);

        $tag = $eventListenerTags[0];
        $this->assertIsArray($tag);
        $this->assertArrayHasKey('event', $tag);
        $this->assertArrayHasKey('priority', $tag);
        $this->assertEquals(UserInteractionEvent::class, $tag['event']);
        $this->assertEquals(-10, $tag['priority']);
    }

    public function testFetchUserInteractionEvents(): void
    {
        // 设置真实的 kernel.bundles 参数
        $this->container->setParameter('kernel.bundles', []);

        // 创建 ListenerCompilerPass 实例并测试 fetchUserInteractionEvents 方法
        $compilerPass = new ListenerCompilerPass();

        // 使用反射访问 public 方法
        $reflection = new \ReflectionClass($compilerPass);
        $method = $reflection->getMethod('fetchUserInteractionEvents');
        $method->setAccessible(true);

        // 执行方法并获取结果
        $result = $method->invoke($compilerPass, $this->container);

        // 验证返回值是可迭代的
        $this->assertIsIterable($result);

        // 将生成器转换为数组以便测试
        $events = iterator_to_array($result);

        // 验证返回的是数组（空数组，因为没有 Bundle）
        $this->assertIsArray($events);
        $this->assertEmpty($events);
    }
}
