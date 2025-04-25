<?php

namespace Tourze\UserTrackBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserTrackBundle\Entity\TrackLog;

class TrackLogTest extends TestCase
{
    public function testGetterAndSetter(): void
    {
        $trackLog = new TrackLog();

        // 测试ID
        $this->assertEquals('0', $trackLog->getId());

        // 测试事件
        $event = 'test.event';
        $this->assertNull($trackLog->getEvent());
        $trackLog->setEvent($event);
        $this->assertEquals($event, $trackLog->getEvent());

        // 测试参数
        $params = ['key1' => 'value1', 'key2' => 'value2'];
        $this->assertIsArray($trackLog->getParams());
        $this->assertEmpty($trackLog->getParams());
        $trackLog->setParams($params);
        $this->assertEquals($params, $trackLog->getParams());

        // 测试创建IP
        $ip = '127.0.0.1';
        $this->assertNull($trackLog->getCreatedFromIp());
        $trackLog->setCreatedFromIp($ip);
        $this->assertEquals($ip, $trackLog->getCreatedFromIp());

        // 测试创建时间
        $now = new \DateTime();
        $this->assertNull($trackLog->getCreateTime());
        $trackLog->setCreateTime($now);
        $this->assertEquals($now, $trackLog->getCreateTime());

        // 测试用户ID
        $userId = '12345';
        $this->assertNull($trackLog->getUserId());
        $trackLog->setUserId($userId);
        $this->assertEquals($userId, $trackLog->getUserId());

        // 测试Reporter用户
        $mockUser = $this->createMock(UserInterface::class);
        $mockUser->method('getUserIdentifier')->willReturn($userId);
        $this->assertNull($trackLog->getReporter());
        $trackLog->setReporter($mockUser);
        $this->assertSame($mockUser, $trackLog->getReporter());

        // 测试renderParamsColumn方法
        $this->assertIsString($trackLog->renderParamsColumn());
        $this->assertStringContainsString('key1', $trackLog->renderParamsColumn());
        $this->assertStringContainsString('value1', $trackLog->renderParamsColumn());

        // 测试retrieveApiArray方法
        $apiArray = $trackLog->retrieveApiArray();
        $this->assertIsArray($apiArray);
        $this->assertArrayHasKey('id', $apiArray);
        $this->assertArrayHasKey('createTime', $apiArray);
        $this->assertArrayHasKey('event', $apiArray);
        $this->assertArrayHasKey('params', $apiArray);
        $this->assertArrayHasKey('createdFromIp', $apiArray);
        $this->assertEquals($event, $apiArray['event']);
    }
}
