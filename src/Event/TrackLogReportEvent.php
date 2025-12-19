<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tourze\UserTrackBundle\Entity\TrackLog;

final class TrackLogReportEvent extends Event
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

    /**
     * @var array<string, mixed>
     */
    private array $params = [];

    /**
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array<string, mixed> $params
     */
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

    /**
     * @var array<string, mixed>
     */
    private array $result = [];

    /**
     * @return array<string, mixed>
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param array<string, mixed> $result
     */
    public function setResult(array $result): void
    {
        $this->result = $result;
    }
}
