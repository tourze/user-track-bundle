<?php

namespace Tourze\UserTrackBundle\Procedure;

use Carbon\Carbon;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\UserTrackBundle\Entity\TrackLog;
use Tourze\UserTrackBundle\Event\TrackLogReportEvent;

#[MethodExpose('SubmitCrmTrackLog')]
#[MethodDoc('提交足迹日志')]
#[IsGranted('IS_AUTHENTICATED')]
class SubmitCrmTrackLog extends LockableProcedure
{
    #[MethodParam('动作')]
    public string $event;

    #[MethodParam('参数列表')]
    public array $params = [];

    public function __construct(
        private readonly DoctrineService $doctrineService,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function execute(): array
    {
        $user = $this->security->getUser();

        $log = new TrackLog();
        $log->setReporter($user);
        $log->setUserId($user->getUserIdentifier());
        $log->setEvent($this->event);
        $log->setParams($this->params);

        $result = [
            'time' => Carbon::now()->getTimestamp(),
        ];

        $event = new TrackLogReportEvent();
        $event->setEvent($this->event);
        $event->setParams($this->params);
        $event->setTrackLog($log);
        $event->setResult($result);
        $this->eventDispatcher->dispatch($event);

        $this->doctrineService->asyncInsert($log);

        return $event->getResult();
    }

    public function getLockResource(JsonRpcParams $params): ?array
    {
        if (!$this->security->getUser()) {
            return null;
        }

        return [
            "{$this->security->getUser()->getUserIdentifier()}-SubmitCrmTrackLog-" . $params->get('event'),
        ];
    }
}
