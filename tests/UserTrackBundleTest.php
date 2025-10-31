<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\UserTrackBundle\UserTrackBundle;

/**
 * @internal
 */
#[CoversClass(UserTrackBundle::class)]
#[RunTestsInSeparateProcesses]
final class UserTrackBundleTest extends AbstractBundleTestCase
{
}
