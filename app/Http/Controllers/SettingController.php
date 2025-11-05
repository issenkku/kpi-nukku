<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('setting.app', compact('setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => ['nullable', 'string', 'max:255'],
            'notify_date1'    => ['nullable', 'date'],
            'notify_time1'    => ['nullable', 'date_format:H:i'],
            'notify_date2'    => ['nullable', 'date'],
            'notify_time2'    => ['nullable', 'date_format:H:i'],
            'message'         => ['nullable', 'string', 'max:500'],
            'remind_days'     => ['nullable', 'string', 'max:50'],
            'remind_time'     => ['nullable', 'date_format:H:i'],
            'remind_enabled'  => ['nullable', 'boolean'],
        ]);

        $validated['title'] = $validated['title'] ?: null;
        $validated['remind_enabled'] = (bool) ($validated['remind_enabled'] ?? false);

        $setting = Setting::updateOrCreate(['id' => 1], $validated);

        // Do not trigger reminders automatically on save; scheduler handles timing.

        return redirect()->route('settings.index')
            ->with('success', 'บันทึกข้อมูลการตั้งค่าแล้ว และสั่งงานแจ้งเตือนทันที');
    }

    public function update(Request $request, $id)
    {
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
        ]);

        if (($validated['title'] ?? '') === '') {
            $validated['title'] = null;
        }
        $validated['remind_enabled'] = (bool) ($validated['remind_enabled'] ?? false);

        $setting = Setting::findOrFail($id);
        $setting->update($validated);

        // Do not trigger reminders automatically on save; scheduler handles timing.

        return redirect()->route('settings.index')
            ->with('success', 'บันทึกข้อมูลการตั้งค่าแล้ว และสั่งงานแจ้งเตือนทันที');
    }
}
