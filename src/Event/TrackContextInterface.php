<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Event;

interface TrackContextInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getTrackingParams(): array;

    /**
     * @param array<string, mixed> $params
     */
    public function setTrackingParams(array $params): void;
}
