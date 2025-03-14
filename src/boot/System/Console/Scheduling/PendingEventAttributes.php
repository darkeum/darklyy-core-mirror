<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Console\Scheduling;

class PendingEventAttributes
{
    use ManagesAttributes, ManagesFrequencies;

    public function __construct(
        protected Schedule $schedule,
    ) {
    }

    /**
     * Не допускайте, чтобы события накладывались друг на друга.
     *
     * Срок действия базовой блокировки кэша может быть указан в минутах.
     *
     * @param  int  $expiresAt
     * @return $this
     */
    public function withoutOverlapping($expiresAt = 1440)
    {
        $this->withoutOverlapping = true;

        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function mergeAttributes(Event $event): void
    {
        $event->expression = $this->expression;
        $event->repeatSeconds = $this->repeatSeconds;

        if ($this->description !== null) {
            $event->name($this->description);
        }

        if ($this->timezone !== null) {
            $event->timezone($this->timezone);
        }

        if ($this->user !== null) {
            $event->user = $this->user;
        }

        if (! empty($this->environments)) {
            $event->environments($this->environments);
        }

        if ($this->evenInMaintenanceMode) {
            $event->evenInMaintenanceMode();
        }

        if ($this->withoutOverlapping) {
            $event->withoutOverlapping($this->expiresAt);
        }

        if ($this->onOneServer) {
            $event->onOneServer();
        }

        if ($this->runInBackground) {
            $event->runInBackground();
        }

        foreach ($this->filters as $filter) {
            $event->when($filter);
        }

        foreach ($this->rejects as $reject) {
            $event->skip($reject);
        }
    }

    public function __call(string $method, array $parameters): mixed
    {
        return $this->schedule->{$method}(...$parameters);
    }
}
