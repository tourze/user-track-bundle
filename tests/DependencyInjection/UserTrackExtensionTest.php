<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\UserTrackBundle\DependencyInjection\UserTrackExtension;

/**
 * @internal
 */
#[CoversClass(UserTrackExtension::class)]
final class UserTrackExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
}
