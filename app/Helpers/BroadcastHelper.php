<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class BroadcastHelper
{
    /**
     * Safely broadcast an event, with fallback if broadcasting fails
     *
     * @param mixed $event The event to broadcast
     * @return bool Whether the broadcast was successful
     */
    public static function safeBroadcast($event): bool
    {
        try {
            event($event);
            return true;
        } catch (\Exception $e) {
            // Log the error but don't propagate it
            Log::warning('Broadcasting failed: ' . $e->getMessage(), [
                'event' => get_class($event),
                'exception' => get_class($e),
            ]);
            return false;
        }
    }
}
