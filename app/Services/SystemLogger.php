<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class SystemLogger
{
    /**
     * Record a system event.
     *
     * Accepts keys: actor_type, actor_id, actor_name, event_type, event_subtype, ip_address, user_agent, payload
     */
    public static function record(array $data)
    {
        $defaults = [
            'actor_type' => null,
            'actor_id' => null,
            'actor_name' => null,
            'event_type' => $data['event_type'] ?? 'unknown',
            'event_subtype' => $data['event_subtype'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'payload' => $data['payload'] ?? null,
        ];

        $actorType = $defaults['actor_type'];
        $actorId = $defaults['actor_id'];
        $actorName = $defaults['actor_name'];

        // If controller didn't provide actor details (or marked as system), try to detect currently authenticated user
        if (empty($actorId) || $actorType === 'system' || empty($actorType)) {
            // Check common guards for an authenticated user
            $guards = ['driver', 'admin', 'lab', null]; // null will check default guard via Auth::user()
            foreach ($guards as $g) {
                try {
                    $user = $g ? Auth::guard($g)->user() : Auth::user();
                } catch (\Throwable $ex) {
                    $user = null;
                }
                if ($user) {
                    // found an authenticated actor
                    $actorId = $user->id ?? null;
                    // determine actor type label
                    $actorType = $g ?: 'user';
                    // determine a friendly actor name/email/phone
                    $actorName = $user->name ?? $user->email ?? $user->phone ?? null;
                    break;
                }
            }
        }

        $payload = $defaults['payload'];
        if (is_array($payload) && empty($payload)) {
            $payload = null;
        }

        // Ensure we have a human-friendly description: payload.description || config fallback
        $eventType = $defaults['event_type'];
        $eventSubtype = $defaults['event_subtype'];
        $description = null;
        if (is_array($payload) && array_key_exists('description', $payload) && !empty($payload['description'])) {
            $description = $payload['description'];
        } else {
            try {
                $conf = config("system_logs.events.{$eventType}.{$eventSubtype}");
                if (!empty($conf)) {
                    $description = $conf;
                }
            } catch (\Throwable $e) {
                // ignore and leave description null
            }
        }

        if ($description) {
            if (!is_array($payload)) $payload = [];
            $payload['description'] = $description;
        }

        $row = array_merge($defaults, [
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'payload' => $payload,
        ]);

        return SystemLog::create($row);
    }
}
