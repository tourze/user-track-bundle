<?php

namespace Tourze\UserTrackBundle\EventSubscriber;

use Psr\Log\LoggerInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;
use Tourze\UserEventBundle\Event\UserInteractionEvent;
use Tourze\UserIDBundle\Model\SystemUser;
use Tourze\UserTrackBundle\Entity\TrackLog;
use Tourze\UserTrackBundle\Event\TrackContextInterface;

/**
 * 将用户行为沉淀到CRM系统这边，方便我们回头做客户雷达
 *
 * @see http://www.woshipm.com/operate/5097185.html
 * @see http://www.zhongheinfo.com/hkt.html
 * @see https://www.dkhd.cn/huoke.html
 */
class UserTrackListener
{
    public function __construct(
        private readonly DoctrineService $doctrineService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(UserInteractionEvent $event): void
    {
        $this->logger->debug(sprintf('[%s] %s 对 %s 说：%s', $event::class, $event->getSender()->getUserIdentifier(), $event->getReceiver()->getUserIdentifier(), $event->getMessage()), [
            'event' => $event,
        ]);

        $user = $event->getSender();

        if ($event->getReceiver() instanceof SystemUser) {
            $log = new TrackLog();
            if (!$user instanceof SystemUser) {
                $log->setReporter($user);
            }
            $log->setUserId($user->getUserIdentifier());
            $log->setEvent($event->getMessage());
            if (empty($log->getEvent())) {
                $log->setEvent(get_class($event));
            }
            if ((bool) $event instanceof TrackContextInterface) {
                $log->setParams($event->getTrackingParams());
            }

            try {
                $this->doctrineService->asyncInsert($log);
            } catch (\Throwable $exception) {
                $this->logger->error('保存客户行为信息时发生异常', [
                    'exception' => $exception,
                    'event' => $event,
                ]);
            }
        }
    }
}
