@extends('layouts.app')

@section('title', 'ตั้งค่าการแจ้งเตือน')
@section('header', 'ตั้งค่าการแจ้งเตือน')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')
    <div class="Setting-card">
        <x-card>
            <div class="card-title">การตั้งค่าการแจ้งเตือน</div>

            <div class="form-group">
                <label class="form-label">ชื่อประกาศ/เรื่อง</label>
                <input form="notification-settings-form" type="text" name="title" class="form-input" aria-label="ชื่อประกาศ/เรื่อง"
                       value="{{ old('title', $setting->title ?? '') }}">

            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label">วันที่แจ้งเตือน 1</label>
                    <input form="notification-settings-form" type="date" name="notify_date1" class="form-input2 inline-input" aria-label="วันที่แจ้งเตือน 1" value="{{ old('notify_date1', $setting?->notify_date1?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">เวลาแจ้งเตือน 1</label>
                    <input form="notification-settings-form" type="time" name="notify_time1" class="form-input2 inline-input" aria-label="เวลาแจ้งเตือน 1" style="width:140px;" value="{{ old('notify_time1', $setting->notify_time1) }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label">วันที่แจ้งเตือน 2</label>
                    <input form="notification-settings-form" type="date" name="notify_date2" class="form-input2 inline-input" aria-label="วันที่แจ้งเตือน 2" value="{{ old('notify_date2', $setting?->notify_date2?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">เวลาแจ้งเตือน 2</label>
                    <input form="notification-settings-form" type="time" name="notify_time2" class="form-input2 inline-input" aria-label="เวลาแจ้งเตือน 2" style="width:140px;" value="{{ old('notify_time2', $setting->notify_time2) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">วันที่แจ้งเตือน 1</label>
                <input form="notification-settings-form" type="date" name="notify_date1" class="form-input2" aria-label="วันที่แจ้งเตือน 1"
                       value="{{ old('notify_date1', $setting?->notify_date1?->format('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label class="form-label">วันที่แจ้งเตือน 2</label>
                <input form="notification-settings-form" type="date" name="notify_date2" class="form-input2" aria-label="วันที่แจ้งเตือน 2"
                       value="{{ old('notify_date2', $setting?->notify_date2?->format('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label class="form-label">ข้อความแจ้งเตือน</label>
                <textarea form="notification-settings-form" name="message" class="form-input" rows="3" placeholder="เช่น กรุณากรอกข้อมูลภายในสิ้นเดือน">{{ old('message', $setting->message ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">เวลาแจ้งเตือน 1</label>
                <input form="notification-settings-form" type="time" name="notify_time1" class="form-input2" aria-label="เวลาแจ้งเตือน 1"
                       style="width:140px;" value="{{ old('notify_time1', $setting->notify_time1) }}">
            </div>

            <div class="form-group">
                <label class="form-label">เวลาแจ้งเตือน 2</label>
                <input form="notification-settings-form" type="time" name="notify_time2" class="form-input2" aria-label="เวลาแจ้งเตือน 2"
                       style="width:140px;" value="{{ old('notify_time2', $setting->notify_time2) }}">
            </div>
        </x-card>

        <form id="notification-settings-form" action="{{ route('settings.store') }}" method="POST">
            @csrf
            <div class="action-bts">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> บันทึกข้อมูลการตั้งค่า
                </button>
                <button type="submit" name="send_now" value="1" formaction="{{ route('settings.sendNow') }}" class="btn btn-secondary">
                    <i class="fa fa-paper-plane"></i> บันทึกและส่งแจ้งเตือนทันที
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css" />
    <style>
        .container { max-width: 960px !important; }
        .Setting-card { display:flex; flex-direction:column; gap: 16px; }
        .card-title { font-weight:600; margin-bottom:10px; }
        .form-group { margin-bottom: 16px; }
        .form-label { display:block; margin-bottom:8px; }
        .form-input, .form-input2 { padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; }
        .form-row { display:flex; gap: 16px; align-items: flex-end; }
        .form-row .form-group { margin-bottom: 0; }
        .action-bts { display:flex; gap: 10px; justify-content:center; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && typeof $.fn.trumbowyg === 'function') {
                const $msg = $('textarea[name="message"]');
                const initial = @json(old('message', $setting->message ?? ''));
                $msg.trumbowyg({ svgPath: 'https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/icons.svg' });
                $msg.trumbowyg('html', initial || '');
            }

            // Hide and disable old separate date/time groups; keep only inline inputs active
            ['notify_date1','notify_time1','notify_date2','notify_time2'].forEach(function(name){
                var nodes = document.querySelectorAll('input[name="'+name+'"]');
                nodes.forEach(function(el){
                    if (!el.classList.contains('inline-input')) {
                        el.disabled = true;
                        var fg = el.closest('.form-group');
                        if (fg) { fg.style.display = 'none'; }
                    }
                });
            });
        });
    </script>
@endpush
