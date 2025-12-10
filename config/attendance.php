<?php

return [
    // Default work day start and end (24h format)
    'work_start' => env('ATT_WORK_START', '08:00:00'),
    'work_end' => env('ATT_WORK_END', '16:00:00'),

    // Minutes after start considered "terlambat" (late threshold)
    'late_threshold_minutes' => env('ATT_LATE_THRESHOLD', 5),

    // Penalty rules (Rp per minute and per hour) - optional, shown in UI
    'penalty_per_minute' => env('ATT_PENALTY_PER_MINUTE', 1000),
    'penalty_per_hour' => env('ATT_PENALTY_PER_HOUR', 60000),
];
