<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Assignment;
use App\Notifications\DeadlineReminderNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SettingNotifyController extends Controller
{
    public function sendNow(Request $request)
    {
        Log::info('[settings] send_now requested', [
            'user_id' => optional($request->user())->id,
        ]);

        $validated = $request->validate([
            'title'          => ['nullable', 'string', 'max:255'],
            'notify_date1'   => ['nullable', 'date'],
            'notify_time1'   => ['nullable', 'date_format:H:i'],
            'notify_date2'   => ['nullable', 'date'],
            'notify_time2'   => ['nullable', 'date_format:H:i'],
            'message'        => ['nullable', 'string', 'max:500'],
            'remind_days'    => ['nullable', 'string', 'max:50'],
            'remind_time'    => ['nullable', 'date_format:H:i'],
            'remind_enabled' => ['nullable', 'boolean'],
            'indicator_id'   => ['nullable', 'integer'],
        ]);

        if (($validated['title'] ?? '') === '') {
            $validated['title'] = null;
        }
        $validated['remind_enabled'] = (bool) ($validated['remind_enabled'] ?? false);

        $setting = Setting::updateOrCreate(['id' => 1], $validated);

        // Clear fixed caches for today so sending now is not blocked by prior runs
        try {
            $today = Carbon::now('Asia/Bangkok')->toDateString();
            Cache::forget("reminder:fixed:d1:$today");
            Cache::forget("reminder:fixed:d2:$today");
        } catch (\Throwable $e) {
            // ignore
        }

        $onlyIndicatorId = isset($validated['indicator_id']) ? (int) $validated['indicator_id'] : null;
        $sent = $this->sendRemindersNow($setting, $onlyIndicatorId);

        return redirect()->route('settings.index')->with('success', 'บันทึกและส่งแจ้งเตือนแล้ว จำนวน ' . $sent . ' รายการ');
    }

    private function sendRemindersNow(Setting $setting, ?int $onlyIndicatorId = null): int
    {
        $title = $setting->title ?: '[KPI] แจ้งเตือนกำหนดส่ง/อัปเดตสถานะ';
        $msg = $setting->message ?: 'โปรดตรวจสอบและดำเนินการตามกำหนด';

        // Resolve recipients from assignments (collectors)
        $assignments = Assignment::query()
            ->when($onlyIndicatorId, fn($q) => $q->where('indicator_id', $onlyIndicatorId))
            ->with('collectorUser')
            ->get();

        $users = $assignments->pluck('collectorUser')->filter()->unique('id')->values();
        Log::info('[settings] send_now recipients resolved', [
            'count' => $users->count(),
            'indicator_id' => $onlyIndicatorId,
        ]);
        $url = route('dashboardkpi.index');

        $sent = 0;
        foreach ($users as $user) {
            try {
                $user->notify(new DeadlineReminderNotification($title, $msg, $url));
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('sendRemindersNow failed for user ' . ($user->id ?? '-') . ' : ' . $e->getMessage());
            }
        }
        Log::info('[settings] send_now dispatched to ' . $sent . ' users (assignees)');
        return $sent;
    }
}

