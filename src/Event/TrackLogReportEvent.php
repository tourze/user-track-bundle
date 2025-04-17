<?php

namespace Tourze\UserTrackBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tourze\UserTrackBundle\Entity\TrackLog;

class TrackLogReportEvent extends Event
{
    private string $event;

    public function getEvent(): string
    {
        return $this->event;
    }

    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    private array $params = [];

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    private TrackLog $trackLog;

    public function getTrackLog(): TrackLog
    {
        return $this->trackLog;
    }

    public function setTrackLog(TrackLog $trackLog): void
    {
        $this->trackLog = $trackLog;
    }

    private array $result = [];

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $result): void
    {
        $this->result = $result;
    }
}
