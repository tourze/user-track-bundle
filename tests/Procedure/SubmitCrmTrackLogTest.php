<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\UserTrackBundle\Procedure\SubmitCrmTrackLog;

/**
 * @internal
 */
#[CoversClass(SubmitCrmTrackLog::class)]
#[RunTestsInSeparateProcesses]
final class SubmitCrmTrackLogTest extends AbstractProcedureTestCase
{
    private SubmitCrmTrackLog $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(SubmitCrmTrackLog::class);
    }

    public function testExecute(): void
    {
        // 创建测试用户
        $user = $this->createNormalUser('test@example.com', 'password123');

        // 设置认证用户
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage = self::getService(TokenStorageInterface::class);
        $tokenStorage->setToken($token);

        // 设置参数
        $this->procedure->event = 'test.event';
        $this->procedure->params = ['key' => 'value'];

        // 执行测试
        $result = $this->procedure->execute();

        // 验证结果
        $this->assertArrayHasKey('time', $result);
        $this->assertIsInt($result['time']);
    }

    public function testGetLockResource(): void
    {
        // 创建测试用户
        $user = $this->createNormalUser('test@example.com', 'password123');

        // 设置认证用户
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage = self::getService(TokenStorageInterface::class);
        $tokenStorage->setToken($token);

        // 创建方法的反射
        $reflectionMethod = new \ReflectionMethod(SubmitCrmTrackLog::class, 'getLockResource');
        $reflectionMethod->setAccessible(true);

        // 创建真实的 JsonRpcParams 对象
        $params = new JsonRpcParams(['event' => 'test.event']);

        // 调用方法
        $result = $reflectionMethod->invoke($this->procedure, $params);

        // 验证结果
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertIsString($result[0]);
        $this->assertEquals('test@example.com-SubmitCrmTrackLog-test.event', $result[0]);
    }

    public function testGetLockResourceWithoutUser(): void
    {
        // 创建方法的反射
        $reflectionMethod = new \ReflectionMethod(SubmitCrmTrackLog::class, 'getLockResource');
        $reflectionMethod->setAccessible(true);

        // 创建真实的 JsonRpcParams 对象
        $params = new JsonRpcParams([]);

        // 调用方法
        $result = $reflectionMethod->invoke($this->procedure, $params);

        // 验证结果
        $this->assertNull($result);
    }
}
