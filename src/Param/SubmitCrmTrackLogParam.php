<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * SubmitCrmTrackLog Procedure 的参数对象
 *
 * 用于提交足迹日志
 */
readonly class SubmitCrmTrackLogParam implements RpcParamInterface
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        #[MethodParam(description: '动作')]
        #[Assert\NotBlank]
        public string $event,

        #[MethodParam(description: '参数列表')]
        public array $params = [],
    ) {
    }
}
