<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;
use Tourze\UserTrackBundle\Entity\TrackLog;
use Tourze\UserTrackBundle\Event\TrackLogReportEvent;

/**
 * @internal
 */
#[CoversClass(TrackLogReportEvent::class)]
final class TrackLogReportEventTest extends AbstractEventTestCase
{
    protected function onSetUp(): void
    {
        // 这个测试类不需要特殊的初始化逻辑
    }

    public function testEventGetterAndSetter(): void
    {
        $event = new TrackLogReportEvent();

        // 测试event属性
        $eventName = 'test.event';
        $event->setEvent($eventName);
        $this->assertEquals($eventName, $event->getEvent());

        // 测试params属性
        $params = ['key1' => 'value1', 'key2' => 'value2'];
        $this->assertEmpty($event->getParams());
        $event->setParams($params);
        $this->assertEquals($params, $event->getParams());

        // 测试trackLog属性
        $trackLog = new TrackLog();
        $trackLog->setEvent($eventName);
        $event->setTrackLog($trackLog);
        $this->assertSame($trackLog, $event->getTrackLog());

        // 测试result属性
        $result = ['status' => 'success'];
        $this->assertEmpty($event->getResult());
        $event->setResult($result);
        $this->assertEquals($result, $event->getResult());
    }
}
