<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\UserTrackBundle\Event\TrackContextInterface;

/**
 * @internal
 */
#[CoversClass(TrackContextInterface::class)]
final class TrackContextTest extends TestCase
{
    public function testTrackContext(): void
    {
        $trackContext = new class implements TrackContextInterface {
            /** @var array<string, mixed> */
            private array $params = [];

            /**
             * @return array<string, mixed>
             */
            public function getTrackingParams(): array
            {
                return $this->params;
            }

            /**
             * @param array<string, mixed> $params
             */
            public function setTrackingParams(array $params): void
            {
                $this->params = $params;
            }
        };

        $this->assertEmpty($trackContext->getTrackingParams());

        $params = ['action' => 'login', 'timestamp' => time()];
        $trackContext->setTrackingParams($params);

        $this->assertEquals($params, $trackContext->getTrackingParams());
        $this->assertArrayHasKey('action', $trackContext->getTrackingParams());
        $this->assertEquals('login', $trackContext->getTrackingParams()['action']);
    }
}
