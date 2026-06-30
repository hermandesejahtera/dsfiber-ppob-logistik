<?php

namespace DSFiber\Infrastructure\Event;

/**
 * Event Bus for Publishing Events
 */
class EventBus
{
    private static array $listeners = [];

    /**
     * Subscribe to event
     */
    public static function subscribe(string $event, callable $callback): void
    {
        if (!isset(self::$listeners[$event])) {
            self::$listeners[$event] = [];
        }
        self::$listeners[$event][] = $callback;
    }

    /**
     * Publish event
     */
    public static function publish(string $event, mixed $data = null): void
    {
        if (!isset(self::$listeners[$event])) {
            return;
        }

        foreach (self::$listeners[$event] as $callback) {
            call_user_func($callback, $data);
        }
    }

    /**
     * Unsubscribe from event
     */
    public static function unsubscribe(string $event): void
    {
        unset(self::$listeners[$event]);
    }
}
