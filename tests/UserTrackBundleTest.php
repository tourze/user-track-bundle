<?php

namespace Tourze\UserTrackBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\UserTrackBundle\DependencyInjection\ListenerCompilerPass;
use Tourze\UserTrackBundle\UserTrackBundle;

class UserTrackBundleTest extends TestCase
{
    public function testBuild(): void
    {
        $bundle = new UserTrackBundle();

        $container = $this->createMock(ContainerBuilder::class);

        // 验证添加编译器
        $container->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->callback(function ($compilerPass) {
                return $compilerPass instanceof ListenerCompilerPass;
            }));

        $bundle->build($container);
    }
}
