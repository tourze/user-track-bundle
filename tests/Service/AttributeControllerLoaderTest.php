<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\UserTrackBundle\Service\AttributeControllerLoader;

/**
 * AttributeControllerLoader服务测试
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // Setup for AttributeControllerLoader tests
    }

    public function testLoad(): void
    {
        $container = self::getContainer();
        /** @var AttributeControllerLoader $loader */
        $loader = $container->get(AttributeControllerLoader::class);
        $routeCollection = $loader->load('test');

        self::assertInstanceOf('Symfony\Component\Routing\RouteCollection', $routeCollection);
    }

    public function testSupports(): void
    {
        $container = self::getContainer();
        /** @var AttributeControllerLoader $loader */
        $loader = $container->get(AttributeControllerLoader::class);

        self::assertFalse($loader->supports('test'));
    }

    public function testAutoload(): void
    {
        $container = self::getContainer();
        /** @var AttributeControllerLoader $loader */
        $loader = $container->get(AttributeControllerLoader::class);
        $routeCollection = $loader->autoload();

        self::assertInstanceOf('Symfony\Component\Routing\RouteCollection', $routeCollection);
    }
}
