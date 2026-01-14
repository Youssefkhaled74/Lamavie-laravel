<?php

use App\Services\SystemLogger;

if (! function_exists('log_event')) {
    function log_event(string $eventType, string $eventSubtype = null, array $payload = [], string $actorType = null)
    {
        return SystemLogger::record([
            'event_type' => $eventType,
            'event_subtype' => $eventSubtype,
            'payload' => $payload,
            'actor_type' => $actorType, // optional
        ]);
    }
}
