<?php
use App\Models\Kehadiran;
use Carbon\Carbon;

// sync terlambat flag based on jam_masuk and attendance config
$workStart = Carbon::createFromFormat('H:i:s', config('attendance.work_start', '08:00:00'));
$late = (int) config('attendance.late_threshold_minutes', 5);

echo "Starting sync_terlambat...\n";

Kehadiran::chunk(200, function($rows) use ($workStart, $late) {
    foreach ($rows as $r) {
        if ($r->jam_masuk) {
            try {
                $jm = Carbon::createFromFormat('H:i:s', $r->jam_masuk);
                $r->terlambat = $jm->greaterThan($workStart->copy()->addMinutes($late));
            } catch (\Exception $e) {
                $r->terlambat = false;
            }
        } else {
            $r->terlambat = false;
        }
        $r->save();
    }
});

echo "sync_terlambat finished.\n";
