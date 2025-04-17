<?php

namespace Tourze\UserTrackBundle\Event;

interface TrackContextInterface
{
    public function getTrackingParams(): array;

    public function setTrackingParams(array $params): void;
}
