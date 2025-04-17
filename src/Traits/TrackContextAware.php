<?php

namespace Tourze\UserTrackBundle\Traits;

trait TrackContextAware
{
    private array $trackParams = [];

    public function getTrackingParams(): array
    {
        return $this->trackParams;
    }

    public function setTrackingParams(array $params): void
    {
        $this->trackParams = $params;
    }
}
