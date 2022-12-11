<?php

namespace App\Util;

use RuntimeException;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

trait WatchableTrait
{
    private Stopwatch $stopwatch;

    // FIXME: $eventName - автоподстановка; см. пример из Console\Command\LockableTrait
    protected function startStopwatch(): void
    {
        if (!class_exists(Stopwatch::class)) {
            throw new RuntimeException('To enable the stopwatch feature you must install the symfony/stopwatch component.');
        }

        $this->stopwatch = new Stopwatch();
        $this->stopwatch->start($this->getName());
    }

    protected function getStopwatchInfo(): string
    {
        $stopwatchEvent = $this->endStopwatch();
        $eventDurationSec = $stopwatchEvent->getDuration() / 1000;

        return sprintf('%s (%s s)', $stopwatchEvent, $eventDurationSec);
    }

    protected function endStopwatch(): StopwatchEvent
    {
        return $this->stopwatch->stop($this->getName());
    }
}