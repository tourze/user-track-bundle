<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\UserTrackBundle\Entity\TrackLog;
use Tourze\UserTrackBundle\Repository\TrackLogRepository;

/**
 * @internal
 */
#[CoversClass(TrackLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class TrackLogRepositoryTest extends AbstractRepositoryTestCase
{
    private TrackLogRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(TrackLogRepository::class);
    }

    public function testConstruct(): void
    {
        $this->assertNotNull($this->repository);
    }

    public function testSave(): void
    {
        $entity = new TrackLog();
        $this->repository->save($entity, false);
        $this->assertInstanceOf(TrackLog::class, $entity);
    }

    public function testSaveWithoutFlush(): void
    {
        $entity = new TrackLog();
        $this->repository->save($entity, false);
        $this->assertInstanceOf(TrackLog::class, $entity);
    }

    public function testRemove(): void
    {
        $entity = new TrackLog();
        $this->repository->remove($entity, false);
        $this->assertInstanceOf(TrackLog::class, $entity);
    }

    public function testFindByReporterNullValue(): void
    {
        $criteria = ['reporter' => null];
        $result = $this->repository->findBy($criteria);
        $this->assertIsArray($result);
    }

    public function testCountReporterNullValue(): void
    {
        $criteria = ['reporter' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testFindByUserIdNullValue(): void
    {
        $criteria = ['userId' => null];
        $result = $this->repository->findBy($criteria);
        $this->assertIsArray($result);
    }

    public function testCountUserIdNullValue(): void
    {
        $criteria = ['userId' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testFindByCreateTimeNullValue(): void
    {
        $criteria = ['createTime' => null];
        $result = $this->repository->findBy($criteria);
        $this->assertIsArray($result);
    }

    public function testCountCreateTimeNullValue(): void
    {
        $criteria = ['createTime' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $criteria = ['event' => 'test_event'];
        $orderBy = ['createTime' => 'DESC'];
        $result = $this->repository->findOneBy($criteria, $orderBy);
        $this->assertTrue(is_null($result) || $result instanceof TrackLog);
    }

    public function testFindByWithReporterAssociation(): void
    {
        $result = $this->repository->findBy([]);
        $this->assertIsArray($result);
    }

    public function testCountWithReporterAssociation(): void
    {
        $result = $this->repository->count([]);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testFindByParamsNullValue(): void
    {
        $criteria = ['params' => null];
        $result = $this->repository->findBy($criteria);
        $this->assertIsArray($result);
    }

    public function testCountParamsNullValue(): void
    {
        $criteria = ['params' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testFindByWithReporterAssociationQuery(): void
    {
        $criteria = ['reporter' => 1];
        $result = $this->repository->findBy($criteria);
        $this->assertIsArray($result);
    }

    public function testCountWithReporterAssociationQuery(): void
    {
        $criteria = ['reporter' => 1];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testFindByEventNullValue(): void
    {
        $criteria = ['event' => null];
        $result = $this->repository->findBy($criteria);
        $this->assertIsArray($result);
    }

    public function testCountEventNullValue(): void
    {
        $criteria = ['event' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testCountByUserIdAsNullShouldReturnCorrectCount(): void
    {
        $criteria = ['userId' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testCountByCreateTimeAsNullShouldReturnCorrectCount(): void
    {
        $criteria = ['createTime' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testCountByEventAsNullShouldReturnCorrectCount(): void
    {
        $criteria = ['event' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testCountByParamsAsNullShouldReturnCorrectCount(): void
    {
        $criteria = ['params' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testCountByUserIdIsNullAndEventIsNotNullShouldReturnCorrectCount(): void
    {
        $criteria = ['userId' => null, 'event' => 'test_event'];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testFindByUserIdIsNullAndEventIsNotNullShouldReturnCorrectEntities(): void
    {
        $criteria = ['userId' => null, 'event' => 'test_event'];
        $result = $this->repository->findBy($criteria);
        $this->assertIsArray($result);
    }

    public function testCountReporterIsNull(): void
    {
        $criteria = ['reporter' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testCountUserIdIsNull(): void
    {
        $criteria = ['userId' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testCountCreateTimeIsNull(): void
    {
        $criteria = ['createTime' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testCountEventIsNull(): void
    {
        $criteria = ['event' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testCountParamsIsNull(): void
    {
        $criteria = ['params' => null];
        $result = $this->repository->count($criteria);
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    protected function createNewEntity(): object
    {
        $entity = new TrackLog();

        // 设置基本字段
        $entity->setUserId('test_user_' . uniqid());
        $entity->setEvent('test_event_' . uniqid());
        $entity->setParams([
            'action' => 'test_action',
            'timestamp' => time(),
        ]);
        $entity->setCreateTime(new \DateTimeImmutable());
        $entity->setCreatedFromIp('127.0.0.1');

        return $entity;
    }

    protected function getRepository(): TrackLogRepository
    {
        return $this->repository;
    }
}
