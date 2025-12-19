<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Tourze\UserTrackBundle\Entity\TrackLog;

final class TrackLogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $trackLog = new TrackLog();
            $trackLog->setUserId('user_' . $i);
            $trackLog->setEvent('test_event_' . $i);
            $trackLog->setParams([
                'action' => 'test_action_' . $i,
                'timestamp' => time(),
                'meta' => ['fixture' => true],
            ]);
            $trackLog->setCreateTime(new \DateTimeImmutable());
            $trackLog->setCreatedFromIp('127.0.0.1');

            $manager->persist($trackLog);
        }

        $manager->flush();
    }
}
