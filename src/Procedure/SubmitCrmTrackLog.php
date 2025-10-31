<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Procedure;

use Carbon\CarbonImmutable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\UserTrackBundle\Entity\TrackLog;
use Tourze\UserTrackBundle\Event\TrackLogReportEvent;

#[MethodTag(name: '用户足迹')]
#[MethodExpose(method: 'SubmitCrmTrackLog')]
#[MethodDoc(summary: '提交足迹日志')]
#[IsGranted(attribute: 'IS_AUTHENTICATED')]
class SubmitCrmTrackLog extends LockableProcedure
{
    #[MethodParam(description: '动作')]
    public string $event;

    /** @var array<string, mixed> */
    #[MethodParam(description: '参数列表')]
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

        if (null === $user) {
            throw new \RuntimeException('User not authenticated');
        }

        $log = new TrackLog();
        $log->setReporter($user);
        $log->setUserId($user->getUserIdentifier());
        $log->setEvent($this->event);
        $log->setParams($this->params);

        $result = [
            'time' => CarbonImmutable::now()->getTimestamp(),
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
        $user = $this->security->getUser();
        if (null === $user) {
            return null;
        }

        $event = $params->get('event');
        assert(is_string($event));

        return [
            "{$user->getUserIdentifier()}-SubmitCrmTrackLog-{$event}",
        ];
    }
}
