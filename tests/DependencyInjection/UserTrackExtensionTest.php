<?php

namespace Tourze\UserTrackBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\UserTrackBundle\DependencyInjection\UserTrackExtension;

class UserTrackExtensionTest extends TestCase
{
    public function testLoad(): void
    {
        $extension = new UserTrackExtension();
        $container = new ContainerBuilder();

        $extension->load([], $container);

        // 检查是否已注册服务
        $this->assertTrue($container->has('Tourze\UserTrackBundle\EventSubscriber\UserTrackListener'));
    }
}
