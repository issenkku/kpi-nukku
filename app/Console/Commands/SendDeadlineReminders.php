<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Indicator;
use App\Models\Setting;
use App\Notifications\DeadlineReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendDeadlineReminders extends Command
{
    protected $signature = 'kpi:send-deadline-reminders';
    protected $description = 'Send reminder emails to assigned users near deadlines and on configured notify dates';

    public function handle(): int
    {
        Log::info('[reminder] command start');
        $setting = Setting::first();
        if (!$setting) {
            Log::info('[reminder] no settings found -> skip');
            $this->info('No settings found. Skipping.');
            return self::SUCCESS;
        }

        // Use application timezone (config('app.timezone'))
        $now = Carbon::now();
        $today = $now->toDateString();

        // 1) Fixed notify dates (round 1 and 2)
        $this->sendByFixedDate($setting, $now);

        // 2) Automatic days-before reminders
        if ($setting->remind_enabled) {
            $this->sendByDaysBefore($setting, $now);
        } else {
            Log::info('[reminder] remind_enabled = false');
        }

        Log::info('[reminder] command end');
        return self::SUCCESS;
    }

    private function sendByFixedDate(Setting $setting, Carbon $now): void
    {
        $pairs = [
            ['date' => $setting->notify_date1, 'time' => $setting->notify_time1, 'key' => 'd1'],
            ['date' => $setting->notify_date2, 'time' => $setting->notify_time2, 'key' => 'd2'],
        ];
        $inRunSent = [];
        foreach ($pairs as $p) {
            if (empty($p['date'])) { Log::info("[reminder] fixed: empty date for {$p['key']}"); continue; }
            $dateStr = Carbon::parse($p['date'])->toDateString();
            if ($now->toDateString() !== $dateStr) { Log::info("[reminder] fixed: today != {$dateStr}"); continue; }

            if (empty($p['time'])) { Log::info("[reminder] fixed: empty time for {$p['key']}"); continue; }
            $time = $p['time'];
            [$hh,$mm] = array_pad(explode(':', $time), 2, '00');
            $trigger = $now->copy()->setTime((int)$hh, (int)$mm, 0);
            // Allow catch-up: if scheduler missed the exact minute, still send once later the same day.
            // Only requirement: current time is at or past the trigger time for today.
            if ($now->lt($trigger)) { Log::info("[reminder] fixed: now < trigger {$time}"); continue; }

            // Avoid duplicate sends within the day/time window and across d1/d2 with same time
            $timeKey = sprintf('%s:%02d%02d', $dateStr, (int)$hh, (int)$mm);
            if (isset($inRunSent[$timeKey])) { Log::info("[reminder] fixed: already sent in run for {$timeKey}"); continue; }
            $cacheKey = sprintf('reminder:fixed:%s', $timeKey);
            $ttl = $now->copy()->endOfDay()->addMinutes(5);
            if (!Cache::add($cacheKey, 1, $ttl)) { Log::info("[reminder] fixed: already sent for {$cacheKey}"); continue; }
            $inRunSent[$timeKey] = true;

            $count = $this->sendToAssignees($setting);
            Log::info("[reminder] fixed: sent to {$count} users");
        }
    }

    private function sendByDaysBefore(Setting $setting, Carbon $now): void
    {
        $time = $setting->remind_time ?: '09:00';
        [$hh,$mm] = array_pad(explode(':', $time), 2, '00');
        $trigger = $now->copy()->setTime((int)$hh, (int)$mm, 0);
        if ($now->lt($trigger)) { Log::info('[reminder] before: now < remind_time'); return; }

        $days = collect(preg_split('/\s*,\s*/', (string) ($setting->remind_days ?? '')))
            ->filter(fn($v) => $v !== '')
            ->map(fn($v) => (int) $v)
            ->filter(fn($v) => $v >= 0)
            ->unique()->values();
        if ($days->isEmpty()) { Log::info('[reminder] before: days empty'); return; }

        // Find indicators whose deadline is today + N days and not finalized (status not in [3,4])
        $targets = Indicator::query()
            ->whereNotNull('deadline')
            ->whereNotIn('status', [3,4])
            ->get(['id','deadline']);

        foreach ($targets as $ind) {
            try {
                $deadlineDate = Carbon::parse($ind->deadline)->startOfDay();
            } catch (\Throwable $e) {
                continue;
            }
            $diff = $now->copy()->startOfDay()->diffInDays($deadlineDate, false);
            if (!$days->contains($diff)) continue;

            $cacheKey = sprintf('reminder:before:%s:ind:%d', $now->toDateString(), $ind->id);
            $ttl = $now->copy()->endOfDay()->addMinutes(5);
            if (!Cache::add($cacheKey, 1, $ttl)) { Log::info("[reminder] before: already sent for {$cacheKey}"); continue; }

            $count = $this->sendToAssignees($setting, $ind->id);
            Log::info("[reminder] before: ind {$ind->id} -> sent to {$count} users");
        }
    }

    private function sendToAssignees(Setting $setting, ?int $onlyIndicatorId = null): int
    {
        $title = $setting->title ?: '[KPI] แจ้งเตือนกำหนดส่ง';
        $msg = $setting->message ?: 'ใกล้ครบกำหนดส่งหลักฐาน โปรดตรวจสอบตัวชี้วัดที่รับผิดชอบ';

        $assignments = Assignment::query()
            ->when($onlyIndicatorId, fn($q) => $q->where('indicator_id', $onlyIndicatorId))
            ->with('collectorUser')
            ->get();

        $uniqueUsers = $assignments->pluck('collectorUser')->filter()->unique('id');
        Log::info('[reminder] send: users=' . $uniqueUsers->count() . ', indicator=' . ($onlyIndicatorId ?? 'all'));
        $url = route('dashboardkpi.index');

        foreach ($uniqueUsers as $user) {
            try {
                $user->notify(new DeadlineReminderNotification($title, $msg, $url));
            } catch (\Throwable $e) {
                // ignore individual failures
            }
        }
        return $uniqueUsers->count();
    }
}
