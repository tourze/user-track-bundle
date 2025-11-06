<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\UserTrackBundle\Entity\TrackLog;

/**
 * @internal
 */
#[CoversClass(TrackLog::class)]
final class TrackLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new TrackLog();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'params' => ['params', ['key' => 'value']],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 这个测试类不需要特殊的初始化逻辑
    }

    public function testGetterAndSetter(): void
    {
        $trackLog = new TrackLog();

        // 测试ID
        $this->assertEquals(null, $trackLog->getId());

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
        $now = new \DateTimeImmutable();
        $this->assertNull($trackLog->getCreateTime());
        $trackLog->setCreateTime($now);
        $this->assertEquals($now, $trackLog->getCreateTime());

        // 测试用户ID
        $userId = '12345';
        $this->assertNull($trackLog->getUserId());
        $trackLog->setUserId($userId);
        $this->assertEquals($userId, $trackLog->getUserId());

        // 测试Reporter用户 - 使用匿名类替代 Mock
        $testUser = new class implements UserInterface {
            public function getUserIdentifier(): string
            {
                return '12345';
            }

            public function getRoles(): array
            {
                return ['ROLE_USER'];
            }

            public function eraseCredentials(): void
            {
            }
        };
        $this->assertNull($trackLog->getReporter());
        $trackLog->setReporter($testUser);
        $this->assertSame($testUser, $trackLog->getReporter());

        // 测试renderParamsColumn方法
        $this->assertStringContainsString('key1', $trackLog->renderParamsColumn());
        $this->assertStringContainsString('value1', $trackLog->renderParamsColumn());

        // 测试retrieveApiArray方法
        $apiArray = $trackLog->retrieveApiArray();
        $this->assertArrayHasKey('id', $apiArray);
        $this->assertArrayHasKey('createTime', $apiArray);
        $this->assertArrayHasKey('event', $apiArray);
        $this->assertArrayHasKey('params', $apiArray);
        $this->assertArrayHasKey('createdFromIp', $apiArray);
        $this->assertEquals($event, $apiArray['event']);

        // 测试 __toString() 方法
        $this->assertStringContainsString('TrackLog#', (string) $trackLog);
        $this->assertStringContainsString($event, (string) $trackLog);
    }
}
