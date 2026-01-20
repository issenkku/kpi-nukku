@extends('layouts.app')

@section('title', 'รายละเอียดตัวบ่งชี้')

@section('content')
    @php
        // --- Normalize input (works with: ['data'=>...] JSON, or $indicator model/array) ---
        $ind = $data['data'] ?? ($data ?? ($indicator ?? null));

        // helper (works for array|object)
        $dg = fn($key, $default = null) => data_get($ind, $key, $default);

        // Basic fields
        $indicatorId = $dg('id') ?? null;
        $year = $dg('year') ?? '-';
        $name = $dg('name') ?? '-';
        $code = $dg('code') ?? '-';
        $type = $dg('type') ?? '-';
        $maxScore = $dg('max_score') ?? '-';
        $deadline = $dg('deadline') ?? '-';
        $status = $dg('status') ?? '-';
        $comment = $dg('comment') ?? '-';
        $descHtml = $dg('description') ?? '-';
        $condHtml = $dg('condition') ?? '-';
        $annoHtml = $dg('annotation') ?? '-';

        // Dates
        $deadlineDisplay = $deadline ? \Carbon\Carbon::parse($deadline)->format('d/m/Y') : '-';

        // Category / Standard (prefer nested, fallback to top-level)
        $categoryName = $dg('category.name') ?? '-';
        $standardName = $dg('category.standard.name') ?? ($dg('standard.name') ?? '-');

        // Criteria (ordered by sequence)
        $criteria = collect($dg('criterias', []))->sortBy('sequence')->values();
        $criteriaList = $criteria->pluck('name')->all();

        // Map: sequence => name (for checklist labels)
        $seqToName = $criteria->mapWithKeys(function ($c) {
            $seq = data_get($c, 'sequence');
            return $seq ? [$seq => data_get($c, 'name')] : [];
        });

        // Assignments -> collectors
        $assignments = collect($dg('assignments', []));
        $collectors = $assignments->map(fn($a) => data_get($a, 'user.name'))->filter()->unique()->values()->all();

        // Distinct departments (from API's departments array)
        $departments = collect($dg('departments', []))->pluck('name')->unique()->values()->all();
        $workGroups = collect($dg('departments', []))
            ->pluck('work_group')
            ->filter()
            ->unique()
            ->values()
            ->all();

// ---------- Checklist ----------
$checklist = collect($dg('checklistItems', []))
    ->map(function ($it) use ($seqToName) {
        $label = collect(data_get($it, 'required_items', []))
            ->map(fn($i) => $seqToName[$i] ?? "ข้อ {$i}")
            ->implode(', ');
        return [
            'label' => $label,
            'score' => (float) data_get($it, 'score', 0),
        ];
    })
    ->reject(fn($r) => ($r['label'] === '' || $r['label'] === '-') && $r['score'] <= 0)
    ->values();

// ---------- Variable & Formula ----------
$vf = (array) $dg('variable_formula', []);

// variables อนุญาตทั้งสตริง หรืออ็อบเจ็กต์ {variable_name, label_name, type, value}
$variablesVF = collect(data_get($vf, 'variables', []))
    ->map(function ($v) {
        $var = data_get($v, 'variable_name');
        $label = data_get($v, 'label_name');
        $vtype = data_get($v, 'type');
        $value = data_get($v, 'value', null);

        return [
            'var' => trim((string) $var),
            'label' => trim((string) $label),
            'vtype' => trim((string) $vtype),
            'value' => $value,
        ];
    })
    // ->reject(fn($r) => $r['var'] === '' && $r['type'] === '' && is_null($r['value']))
    ->values();

// formulas อนุญาตทั้งสตริง หรืออ็อบเจ็กต์ {condition} / {expression}
$formulasVF = collect(data_get($vf, 'formulas', []))
    ->map(function ($f) {
        $raw = is_string($f) ? $f : data_get($f, 'condition') ?? data_get($f, 'expression', '');
        return trim((string) $raw);
    })
    ->filter()
    ->values();

// flags
$hasVFVars = $variablesVF->isNotEmpty();
$hasVFFx = $formulasVF->isNotEmpty();
$hasChecklist = $checklist->isNotEmpty();

// โชว์ตามโหมด (และซ่อนส่วนว่างอัตโนมัติ)
$showVFSection = $type === 'variable_formula' || ($type !== 'checklist' && ($hasVFVars || $hasVFFx));
$showChecklistSection = $type === 'checklist' || ($type !== 'variable_formula' && $hasChecklist);

    @endphp
    <div class="w-full mx-auto">
        {{-- Header --}}
        <div class="banner rounded-t-2xl border border-slate-200 p-5 ">
            <h1 class="text-2xl sm:text-3xl text-center font-bold">รายละเอียดตัวบ่งชี้</h1>
        </div>
        <div class="mb-5 w-full px-4 sm:px-6 lg:px-8 py-6 bg-white rounded-b-2xl border border-slate-200 shadow-sm">
            <div class="space-y-6 sm:space-y-5">
                {{-- Card 1: Basic --}}
                <x-card number="1" title="ข้อมูลตัวบ่งชี้">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <div class="text-sm text-slate-500">ปีการประเมิน</div>
                            <div class="font-medium text-slate-900">{{ $year ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-slate-500">คะแนนตัวบ่งชี้</div>
                            <div class="font-medium text-slate-900">
                                {{ $maxScore !== null ? number_format((float) $maxScore, 2) : '-' }}
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <div class="text-sm text-slate-500">ชื่อตัวบ่งชี้</div>
                            <div class="font-medium text-slate-900">{{ $name ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-slate-500">รหัสตัวบ่งชี้</div>
                            <div class="font-medium text-slate-900">{{ $code ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-slate-500">มาตรฐานตัวบ่งชี้</div>
                            <div class="font-medium text-slate-900">{{ $standardName }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-slate-500">ด้านตัวบ่งชี้</div>
                            <div class="font-medium text-slate-900">{{ $categoryName }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-slate-500 ">ประเภทตัวบ่งชี้</div>
                            <div class="font-medium text-slate-900">{{ $type ?: '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-slate-500">สถานะตัวบ่งชี้</div>
                            <div class="mt-1">
                                <x-status-badge :status="$status" size="sm" />
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-slate-500">วันสิ้นสุดการประเมิน</div>
                            <div class="font-medium text-slate-900">{{ $deadlineDisplay }}</div>
                        </div>
                    </div>
                </x-card>

                {{-- Card 2: Responsible --}}
                <x-card number="2" title="ผู้รับผิดชอบ">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-slate-500 mb-1">หน่วยงานที่รับผิดชอบ</div>
                            @if (count($departments))
                                <ol class="list-decimal list-inside space-y-1 text-slate-900">
                                    @foreach ($departments as $department)
                                        <li>{{ $department }}</li>
                                    @endforeach
                                </ol>
                            @else
                                <div class="text-slate-400">-</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-sm text-slate-500 mb-1">สังกัดงาน</div>
                            @if (count($workGroups))
                                <ol class="list-decimal list-inside space-y-1 text-slate-900">
                                    @foreach ($workGroups as $workGroup)
                                        <li>{{ $workGroup }}</li>
                                    @endforeach
                                </ol>
                            @else
                                <div class="text-slate-400">-</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-sm text-slate-500 mb-1">ผู้รับผิดชอบในการรวบรวมข้อมูล</div>
                            @if (count($collectors))
                                <ol class="list-decimal list-inside space-y-1 text-slate-900">
                                    @foreach ($collectors as $collector)
                                        <li>{{ $collector }}</li>
                                    @endforeach
                                </ol>
                            @else
                                <div class="text-slate-400">-</div>
                            @endif
                        </div>
                    </div>
                </x-card>


                {{-- Card 3: Description (richtext) --}}
                <x-card number="3" title="คำอธิบายตัวบ่งชี้">
                    <x-richtext-content :html="$descHtml" empty="-" />
                </x-card>

                {{-- Card 4: Criteria --}}
                <x-card number="4" title="เกณฑ์การพิจารณา">
                    @if (count($criteriaList))
                        <ol class="list-decimal pl-5 space-y-1 text-slate-800">
                            @foreach ($criteriaList as $text)
                                <li>{{ $text }}</li>
                            @endforeach
                        </ol>
                    @else
                        <div class="text-slate-400">-</div>
                    @endif
                    <br />
                    <x-card-box title="วิธีการคำนวณ" icon="📋">
                        <x-richtext-content :html="$condHtml" empty="-" />
                    </x-card-box>
                </x-card>

                {{-- Card 5: Scoring (comment richtext + variable/formula + checklist rules) --}}
                <x-card number="5" title="เกณฑ์การให้คะแนน" class="space-y-6" x-data="{ copiedId: null }">
                    {{-- คำอธิบาย --}}
                    <div class="space-y-1">
                        <div class="text-sm font-medium text-slate-700">คำอธิบาย</div>
                        <x-richtext-content :html="$comment" empty="ไม่มีคำอธิบายเกณฑ์" />
                    </div>

                    {{-- ตัวแปร/สูตร --}}
                    @if ($showVFSection && ($hasVFVars || $hasVFFx))
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium text-slate-700">ตัวแปรและสูตรคำนวณ</div>

                                {{-- Legend --}}
                                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
                                    <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">defined</span>
                                    <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-800">input</span>
                                    <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-800">output</span>
                                </div>
                            </div>

                            {{-- 2-column layout on large screens --}}
                            <div class="grid gap-4 lg:grid-cols-2">
                                {{-- Variables table --}}
                                @if ($hasVFVars)
                                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                        <div
                                            class="px-3 py-2 text-sm font-medium text-slate-700 bg-slate-50 border-b border-slate-200">
                                            รายชื่อตัวแปร
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full text-sm text-slate-800">
                                                <thead class="bg-slate-50">
                                                    <tr class="text-left">
                                                        <th class="px-3 py-2 font-semibold border-b border-slate-200">
                                                            ตัวแปร</th>
                                                        <th class="px-3 py-2 font-semibold border-b border-slate-200">
                                                            ป้ายชื่อ</th>
                                                        <th class="px-3 py-2 font-semibold border-b border-slate-200">
                                                            ประเภท</th>
                                                        <th class="px-3 py-2 font-semibold border-b border-slate-200">
                                                            ค่าเริ่มต้น</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($variablesVF as $v)
                                                        @php
                                                            $var = $v['var'] ?? null;
                                                            $label = $v['label'] ?? null;
                                                            $vtype = $v['vtype'] ?? null;
                                                            $value = $v['value'] ?? null;

                                                            $badgeClass = match ($vtype) {
                                                                'defined' => 'bg-blue-100 text-blue-800',
                                                                'input' => 'bg-green-100 text-green-800',
                                                                'output' => 'bg-purple-100 text-purple-800',
                                                                default => 'bg-slate-100 text-slate-700',
                                                            };
                                                            $badgeText = $vtype ?: 'unknown';
                                                        @endphp
                                                        <tr class="odd:bg-white even:bg-slate-50">
                                                            <td class="px-3 py-2 font-medium">{{ $var ?: '-' }}</td>
                                                            <td class="px-3 py-2 text-slate-600">{{ $label ?: '-' }}
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                <span
                                                                    class="px-2 py-1 text-xs rounded-full {{ $badgeClass }}">
                                                                    {{ $badgeText }}
                                                                </span>
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                @if (is_null($value))
                                                                    -
                                                                @elseif (is_bool($value))
                                                                    {{ $value ? 'true' : 'false' }}
                                                                @else
                                                                    {{ $value }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                {{-- Formulas list --}}
                                @if ($hasVFFx)
                                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                        <div
                                            class="px-3 py-2 text-sm font-medium text-slate-700 bg-slate-50 border-b border-slate-200">
                                            สูตร/เงื่อนไข
                                        </div>

                                        <div class="p-3 space-y-2">
                                            @foreach ($formulasVF as $i => $fx)
                                                <div class="group relative">
                                                    <pre
                                                        class="whitespace-pre-wrap leading-relaxed font-mono text-[13px] bg-slate-50 rounded-md px-3 py-2 border border-slate-200">{{ $fx }}</pre>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- เช็กลิสต์ --}}
                    @if ($showChecklistSection && $hasChecklist)
                        <div class="space-y-2">
                            <div class="text-sm font-medium text-slate-700">เกณฑ์ให้คะแนนแบบเช็กลิสต์</div>

                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm text-slate-800">
                                        <thead class="bg-slate-50">
                                            <tr class="text-left">
                                                <th class="px-3 py-2 font-semibold border-b border-slate-200">รายการ
                                                </th>
                                                <th
                                                    class="px-3 py-2 font-semibold border-b border-slate-200 w-40 text-right">
                                                    คะแนน</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($checklist as $r)
                                                <tr class="odd:bg-white even:bg-slate-50">
                                                    <td class="px-3 py-2">
                                                        @if (str_contains($r['label'], ','))
                                                            <ul class="list-disc list-inside space-y-1 text-slate-800">
                                                                @foreach (explode(',', $r['label']) as $item)
                                                                    <li class="text-sm ">{{ trim($item) }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <div class="text-sm text-slate-800">{{ $r['label'] }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 text-right font-medium">
                                                        {{ number_format($r['score'] ?? 0, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ว่างทั้งสองฝั่ง --}}
                    @if (!($showVFSection && ($hasVFVars || $hasVFFx)) && !($showChecklistSection && $hasChecklist))
                        <div class="flex items-center gap-2 text-slate-400 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M8.257 3.099c.366-.446 1.12-.446 1.486 0l6.347 7.732c.43.524.058 1.169-.743 1.169H2.653c-.801 0-1.173-.645-.743-1.169l6.347-7.732z" />
                            </svg>
                            ไม่มีข้อมูลสำหรับตัวแปร/สูตร หรือเช็กลิสต์
                        </div>
                    @endif
                </x-card>

                {{-- Card 6: Annotation/Note (richtext) --}}
                <x-card number="6" title="หมายเหตุ">
                    <x-richtext-content :html="$annoHtml" empty="-" />
                </x-card>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row justify-between gap-4 pt-2">
                    <a href="{{ route('indicator.index') }}" class="btn btn-outline">
                        <i class="fa fa-undo"></i> กลับ
                    </a>

                    <div class="flex flex-col sm:flex-row gap-3 order-1 sm:order-2">
                        <form id="del-indicator-{{ $indicatorId }}"
                            action="{{ route('indicator.delete', $dg('id')) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <x-modal title="ยืนยันการลบตัวบ่งชี้" size="sm">
                                <x-slot:trigger>
                                    <button type="button" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> ลบตัวบ่งชี้
                                    </button>
                                </x-slot:trigger>
                                <div class="space-y-3">
                                    <p class="text-slate-700">
                                        ต้องการลบตัวบ่งชี้ <span
                                            class="font-semibold text-pretty">{{ $name }}</span> หรือไม่?
                                    </p>
                                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                        <p class="text-sm text-red-700">
                                            <i class="fa fa-exclamation-triangle mr-1"></i>
                                            <strong>คำเตือน:</strong> การลบจะไม่สามารถย้อนกลับได้ และจะส่งผลต่อข้อมูลที่เกี่ยวข้องทั้งหมด
                                        </p>
                                    </div>
                                </div>
                                <x-slot:footer>
                                    <div class="flex justify-between gap-3 w-full">
                                        <button type="button" class="btn btn-outline flex-1"
                                            @click="$dispatch('modal:close')">
                                            <i class="fa fa-times mr-1"></i> ยกเลิก
                                        </button>
                                        <button type="button" class="btn btn-danger flex-1"
                                            onclick="document.getElementById('del-indicator-{{ $indicatorId }}').submit()">
                                            <i class="fa fa-trash mr-1"></i> ยืนยันการลบ
                                        </button>
                                    </div>
                                </x-slot:footer>
                            </x-modal>
                        </form>

                        {{-- Enable when edit route is ready --}}
                        <a href="{{ route('indicator.edit', $indicatorId) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> แก้ไข
                        </a>
                        @can('edit-indicator')
                            @php $indicatorId = ($data['data']['id'] ?? ($data['id'] ?? ($indicator->id ?? null))); @endphp
                            @if (!empty($indicatorId))
                                {{-- @if (\Illuminate\Support\Facades\Route::has('indicator.notify')) --}}
                                <form method="POST" action="{{ route('notify', ['id' => $indicatorId]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-paper-plane"></i>ส่งแจ้งเตือนผู้รับมอบหมาย
                                    </button>
                                </form>
                                {{-- @endif --}}
                            @endif
                        @endcan
                        {{-- <button type="button"
                            class="btn btn-primary">
                            แจ้งเตือนผู้รับผิดชอบ
                        </button> --}}
                    </div>
                </div>
            </div> {{-- /space-y --}}
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom banner gradient with a modern, soft look */
        .banner {
            background: linear-gradient(90deg, #e0f2fe 0%, #fef3e0 100%);
            transition: all 0.3s ease-in-out;
        }

        /* Smooth hover effects for buttons */
        .action-btn {
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }
    </style>
@endpush
