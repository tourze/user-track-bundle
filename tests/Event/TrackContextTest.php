<?php

namespace Tourze\UserTrackBundle\Tests\Event;

use PHPUnit\Framework\TestCase;
use Tourze\UserTrackBundle\Event\TrackContextInterface;

class TrackContextTest extends TestCase
{
    public function testTrackContext(): void
    {
        $trackContext = new class implements TrackContextInterface {
            private array $params = [];

            public function getTrackingParams(): array
            {
                return $this->params;
            }

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
