<?php

namespace Tourze\UserTrackBundle\Tests\Event;

use PHPUnit\Framework\TestCase;
use Tourze\UserTrackBundle\Entity\TrackLog;
use Tourze\UserTrackBundle\Event\TrackLogReportEvent;

class TrackLogReportEventTest extends TestCase
{
    public function testEventGetterAndSetter(): void
    {
        $event = new TrackLogReportEvent();

        // 测试event属性
        $eventName = 'test.event';
        $event->setEvent($eventName);
        $this->assertEquals($eventName, $event->getEvent());

        // 测试params属性
        $params = ['key1' => 'value1', 'key2' => 'value2'];
        $this->assertIsArray($event->getParams());
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
        $this->assertIsArray($event->getResult());
        $this->assertEmpty($event->getResult());
        $event->setResult($result);
        $this->assertEquals($result, $event->getResult());
    }
}
