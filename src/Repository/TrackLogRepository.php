<?php

namespace Tourze\UserTrackBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\UserTrackBundle\Entity\TrackLog;

/**
 * @method TrackLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrackLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrackLog[]    findAll()
 * @method TrackLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackLog::class);
    }
}
