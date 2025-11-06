<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;

/**
 * SnowflakeIdGenerator 的 Mock 实现
 *
 * 用于测试环境，生成简单的可预测ID而不是复杂的Snowflake ID
 */
class MockSnowflakeIdGenerator extends AbstractIdGenerator
{
    private static int $counter = 1;

    public function generateId(?EntityManagerInterface $em, ?object $entity): string
    {
        // 如果实体已有ID，直接返回
        if (null !== $entity && method_exists($entity, 'getId')) {
            $existingId = $entity->getId();
            if (null !== $existingId && '' !== $existingId) {
                return $existingId;
            }
        }

        // 生成格式：测试ID + 时间戳 + 计数器
        return 'test' . (time() % 100000) . (self::$counter++);
    }
}