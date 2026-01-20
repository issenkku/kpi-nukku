@extends('layouts.app')

@section('title', '(Read-Only) ' . $indicator->code . ' : ' . $indicator->name)

@section('content')

    @php
        $locked = true; // Force read-only mode
    @endphp

    <div class="indicator-card">
        <div class="indicator-header-container">
            <h1 class="indicator-title">
                {{ $indicator->name }} ({{ $indicator->code }})
            </h1>
            <div class="indicator-tabs">
                <span class="tab">{{ $indicator->category->standard->name ?? '-' }}</span>
                <span class="tab-for-divider">|</span>
                <span class="tab">{{ $indicator->category->name ?? '-' }}</span>
            </div>

            <hr class="tab-divider">
            <div class="info-container">
                <div class="info-row">
                    <span class="label">หน่วยงานที่รับผิดชอบ:</span>
                    @forelse($indicator->assignments as $assignment)
                        @if ($assignment->collectorUser)
                            <span class="chip">
                                {{ $assignment->collectorUser->department->name }}
                            </span>
                        @endif
                    @empty
                        <span class="value">-</span>
                    @endforelse
                </div>

                @php
                    $workGroups = $indicator->assignments
                        ->map(fn($a) => optional(optional($a->collectorUser)->department)->work_group)
                        ->filter()
                        ->unique()
                        ->values();
                @endphp
                <div class="info-row">
                    <span class="label">สังกัดงาน:</span>
                    @forelse($workGroups as $workGroup)
                        <span class="chip">
                            {{ $workGroup }}
                        </span>
                    @empty
                        <span class="value">-</span>
                    @endforelse
                </div>

                <div class="info-row">
                    <span class="label">ผู้รับผิดชอบในการรวบรวม:</span>
                    @forelse($indicator->assignments as $assignment)
                        @if ($assignment->collectorUser)
                            <span class="chip">
                                {{ $assignment->collectorUser->name }}
                            </span>
                        @endif
                    @empty
                        <span class="value">-</span>
                    @endforelse
                </div>

                <div class="info-row">
                    <span class="label">สถานะตัวบ่งชี้:</span>
                    <x-status-badge :status="$indicator->status" size="sm" />
                </div>

            </div>
        </div>
        <hr class="tab-divider">
        <div class="card">
            <h2 class="card-title">คำอธิบายตัวบ่งชี้</h2>
            <div class="description-box">
                {!! $indicator->description ?? '-' !!}
            </div>
        </div>
        <div class="card">
            <h2 class="card-title">เกณฑ์การพิจารณา</h2>
            @forelse($indicator->criterias as $criteriaIndex => $criteria)
                <div class="criteria-box" id="criteria-{{ $criteria->id }}">
                    <div class="criteria-header">
                        <div class="criteria-title">
                            {{ $criteria->sequence }}. {!! $criteria->name !!}
                        </div>
                        @php
                            $statuscriteria = match ($criteria->status) {
                                1 => 'เอกสารครบถ้วน',
                                2 => 'เอกสารไม่ครบถ้วน',
                                default => 'รอดำเนินการ',
                            };

                            $statusColor = match ($criteria->status) {
                                1 => 'bg-[#d1fae5] text-[#065f46]',
                                2 => 'bg-[#fee2e2] text-[#991b1b]',
                                default => 'bg-[#fef3c7] text-[#92400e]',
                            };

                        @endphp
                        <div class="criteria-status {{ $statusColor }}">
                            <label>
                                {{ $statuscriteria }}
                            </label>
                        </div>
                    </div>
                    <div class="criteria-content">
                        {{-- คำอธิบายเกณฑ์ --}}
                        @if ($criteria->description)
                            <div class="criteria-description">
                                {!! $criteria->description !!}
                            </div>
                        @endif

                        @php
                            $detailEvidence = $criteria->evidences
                                ->sortByDesc(function($e){ return $e->created_at; })
                                ->first(function($e){ return filled($e->detail); });
                        @endphp
                        @if ($detailEvidence)
                            <div class="criteria-detail mb-3">
                                <div class="font-semibold text-gray-800 mb-1">รายงานผลการดำเนินงาน</div>
                                <div class="prose max-w-none text-sm text-gray-800">
                                    {!! $detailEvidence->detail !!}
                                </div>
                            </div>
                        @endif

                        @if ($criteria->evidences->isNotEmpty())
                        <div class="evidence-list evidence-list-{{ $criteria->id }}">
                            @forelse($criteria->evidences as $evidence)
                                @php
                                    $type = strtolower($evidence->type ?? '');
                                    $name = strtolower($evidence->name ?? '');
                                @endphp
                                <div class="evidence-item" id="evidence-{{ $evidence->id }}">
                                    <div class="flex items-center space-x-2">
                                        <span class="evidence-icon">
                                            @if (Str::endsWith($type, 'pdf'))
                                                <i data-lucide="file-text" style="color:#dc2626;"></i>
                                            @elseif (Str::endsWith($type, 'doc') || Str::endsWith($type, 'docx') || Str::endsWith($name, '.docx'))
                                                <i data-lucide="file-text" style="color:#2563eb;"></i>
                                            @elseif (Str::endsWith($type, 'ppt') || Str::endsWith($type, 'pptx') || Str::endsWith($name, '.pptx'))
                                                <i data-lucide="presentation" style="color:#eb7e25;"></i>
                                            @elseif (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'image']))
                                                <i data-lucide="image" style="color:#16a34a;"></i>
                                            @elseif (Str::endsWith($type, 'xls') || Str::endsWith($type, 'xlsx') || Str::contains($name, '.xls'))
                                                <i data-lucide="file-spreadsheet" style="color:#059669;"></i>
                                            @elseif ($type === 'url')
                                                <i data-lucide="link" style="color:#9333ea;"></i>
                                            @elseif ($type === 'note')
                                                <i data-lucide="sticky-note" style="color:#f59e0b;"></i>
                                            @else
                                                <i data-lucide="file" style="color:#6b7280;"></i>
                                            @endif
                                        </span>
                                        <span class="evidence-name">
                                            @php
                                                $ext = strtolower(pathinfo($evidence->name, PATHINFO_EXTENSION));
                                                $openInNewTab = $type === 'url' || in_array($ext, [
                                                    'pdf',
                                                    'jpg',
                                                    'jpeg',
                                                    'png',
                                                    'gif',
                                                    'svg',
                                                    'txt',
                                                    'csv',
                                                    'htm',
                                                    'html',
                                                ]);
                                            @endphp

                                            @if ($openInNewTab)
                                                {{-- PDF & Image → เปิดในแท็บใหม่ --}}
                                                <span id="evidence-link-{{ $evidence->id }}">
                                                    <a href="{{ route('evidences.download', $evidence->id) }}"
                                                        target="_blank" rel="noopener noreferrer"
                                                        class="text-blue-600 underline hover:text-blue-800">
                                                        <span
                                                            id="evidence-name-text-{{ $evidence->id }}">{{ $evidence->name }}</span>
                                                    </a>
                                                </span>
                                            @else
                                                {{-- Word, Excel, PPT → ดาวน์โหลด --}}
                                                <span id="evidence-link-{{ $evidence->id }}">
                                                    @php
                                                        $ext = $evidence->type ? ('.' . ltrim($evidence->type, '.')) : '';
                                                        $downloadName = $evidence->name;
                                                        if ($ext && !\Illuminate\Support\Str::endsWith(strtolower($downloadName), strtolower($ext))) {
                                                            $downloadName .= $ext;
                                                        }
                                                    @endphp
                                                    <a href="{{ route('evidences.download', $evidence->id) }}" download="{{ $downloadName }}"
                                                        class="text-blue-600 underline hover:text-blue-800">
                                                        <span
                                                            id="evidence-name-text-{{ $evidence->id }}">{{ $evidence->name }}</span>
                                                    </a>
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center">----- ยังไม่มีเกณฑ์การพิจารณา -----</p>
            @endforelse
        </div>
        @php
            $condition = $indicator->condition ?? '';
            $trimmed = trim($condition);
            $hasImage = preg_match('/<img\s[^>]*src=["\']?([^>"\']+)["\']?/i', $trimmed);
            $hasText = trim(strip_tags($trimmed)) !== '';
        @endphp

        @if ($hasImage || $hasText)
            <div class="card">
                <h2 class="card-title">วิธีการคำนวน</h2>
                <div class="criteria-box">
                    {!! $indicator->condition !!}
                </div>
            </div>
        @endif

        <div class="card">
            <h2 class="card-title">เกณฑ์การให้คะแนน</h2>
            <div class="criteria-box list-disc list-inside">
                {!! $indicator->comment ?? '-' !!}
            </div>
        </div>

        @if ($indicator->variables->where('type', 'input')->isNotEmpty())
            <div class="card">
                <h2 class="card-title">ข้อมูลตัวแปร</h2>
                @php
                    $inputVariables = $indicator->variables->filter(fn($v) => trim($v->type) === 'input');
                @endphp
                @forelse($inputVariables as $variable)
                    <div class="variable-row">
                        <label class="variable-label">
                            {{ $variable->label_name ?? $variable->variable_name }}
                        </label>
                        <div class="variable-display">
                            {{ $variable->value ?? '-' }}
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">ยังไม่มีตัวแปรที่ต้องกรอก</p>
                @endforelse
            </div>
        @endif

        <div class="card">
            <h2 class="card-title">หมายเหตุ</h2>
            <div class="annotation-box">
                {!! $indicator->annotation ?? '-' !!}
            </div>
        </div>

        @if (in_array($indicator->status, [3, 4]))
            <div class="card">
                <h2 class="card-title">คะแนนที่ได้</h2>
                <div class="score-display-container">
                    <div class="score-item">
                        <div class="score-label">คะแนนที่ได้</div>
                        <div class="score-value-display current-score">
                            {{ $indicator->score_acc ?? '0' }}
                        </div>
                    </div>
                    <div class="score-separator">/</div>
                    <div class="score-item">
                        <div class="score-label">คะแนนเต็ม</div>
                        <div class="score-value-display max-score">
                            {{ $indicator->max_score ?? '0' }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="action-bts">
            <button type="button" class="btn btn-outline" id="back-btn"
                onclick="location.href='{{ route('dashboardkpi.index') }}'">
                <i class="fa fa-undo"></i> กลับ
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600&family=Kanit:wght@400;600&family=Sarabun:wght@400;600&display=swap"
        rel="stylesheet">



    <script>
        // Initialize Lucide icons for read-only view
        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }
    </script>
@endpush

@push('styles')
    <style>
        .container {
            max-width: 960px !important;
        }


        .action-bts {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
        }

        .indicator-header-container {
            background: var(--color-white);
            padding: 0 16px;
        }

        .card {
            background: var(--color-white);
            border-radius: var(--radius-default);
            box-shadow: var(--shadow-default);
            padding: 24px;
            border: 1px solid #f3f4f6;
        }

        .card_total {
            background: var(--color-white);
            border-radius: var(--radius-default);
            box-shadow: var(--shadow-default);
            padding: 24px;
            margin: 16px;
            border: 1px solid #f3f4f6;
        }

        .card-title {
            font-size: 18px;
            color: var(--blue-default);
            margin: 0 0 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            padding-left: 10px;
        }

        .card-title::before {
            content: "";
            width: 4px;
            height: 20px;
            border-radius: 8px;
            background: var(--blue-default);
            position: absolute;
            left: 0;
            top: 2px;
            opacity: .25;
        }

        .section-divider {
            position: relative;
            left: -29px;
            width: calc(100% + 57px);
            border: none;
            border-bottom: 3px solid #C3D8E8;
            margin: 24px 0;
        }

        .description-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 14px;
            /* line-height: 1.7; */
            color: #374151;
            text-align: left;
        }

        .description-box p {
            margin-bottom: 6px;
        }

        .description-box ul {
            margin: 8px 0 8px 20px;
            list-style: disc;
        }

        .description-box ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin: 0.5rem 0;
        }

        .description-box ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin: 0.5rem 0;
        }

        .description-box li {
            margin: 0.25rem 0;
        }

        .annotation-card {
            background: var(--color-white);
            color: #92400e;
        }

        .annotation-header {
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #b45309;
        }

        .annotation-body {
            font-size: 14px;
            /* line-height: 1.6; */
            color: #78350f;
        }

        .annotation-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 14px;
            /* line-height: 1.7; */
            color: #374151;
            text-align: left;
        }

        .annotation-box p {
            margin-bottom: 6px;
        }

        .annotation-box ul {
            margin: 8px 0 8px 20px;
            list-style: disc;
        }


        .criteria-box {
            background: var(--color-white);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            text-align: left;
        }

        .criteria-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 8px;
        }

        .criteria-title {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
        }

        .criteria-status {
            border: 1px solid #e5e7eb;
            padding: 3px 0px;
            font-size: 12px;
            font-weight: 500;
            min-width: 130px;
            text-align: center;
        }

        .criteria-description {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .criteria-content {
            padding: 10px;
            border: 1px solid #f0f9ff;
            border-radius: 12px;
        }

        .criteria-evidence {
            padding: 10px;
            display: flex;
            justify-content: center;
        }

        .criteria-box ul {
            list-style-type: disc;
            list-style-position: outside;
            padding-left: 1.5rem;
        }

        .criteria-box ol {
            list-style-type: decimal;
            margin-left: 1.5rem;
            padding-left: 1.5rem;
        }

        .evidence-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .evidence-item {
            background: #f0f9ff;
            border-radius: 8px;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            column-gap: 10px;
            font-size: 13px;
            color: #374151;
            overflow: hidden;
        }

        .evidence-name {
            max-width: 550px;
            word-break: break-word;
            text-align: left;

        }

        .evidence-icon {
            /* margin-right: 6px; */
        }

        .file-icon {
            flex-shrink: 0;
        }

        .total-score-card {
            margin-top: 20px;
            padding: 20px;
            background: #deedfb;
            border-radius: 16px;
            text-align: center;
        }

        .total-score-row {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 8px 0;
            font-size: 16px;
        }

        .total-score-row .label {
            font-weight: 600;
            color: #374151;
        }

        .score-value {
            color: #2563eb;
            font-weight: 700;
            font-size: 20px;
        }

        .score-max {
            color: #10b981;
            font-weight: 700;
            font-size: 20px;
        }

        .variable-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9f9f9;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .variable-label {
            font-weight: 600;
            font-size: 14px;
            color: var(--color-gray-700);
        }

        .variable-display {
            border: 1px solid var(--color-gray-200);
            border-radius: 6px;
            padding: 6px 10px;
            width: 300px;
            text-align: center;
            font-size: 14px;
            background: var(--color-gray-50);
            color: var(--color-gray-700);
        }

        /* Indicator Card */
        .indicator-card {
            display: flex;
            flex-direction: column;
            background: var(--color-white);
            border-radius: var(--radius-default);
            box-shadow: var(--shadow-default);
            border: 1px solid var(--color-gray-100);
            padding: 24px;
            gap: 24px;
        }

        /* Title */
        .indicator-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--color-gray-800);
            margin-bottom: 16px;
            text-align: center;
        }

        /* Tabs */
        .indicator-tabs {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
        }

        .tab {
            color: var(--color-gray-500);
            cursor: default;
        }

        hr.tab-divider {
            border: none;
            color: var(--color-gray-300);
            border-bottom: 2px solid var(--color-gray-300);
            margin: 16px 0;
        }


        /* Info container */
        .info-container {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .info-row {
            font-size: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .info-row .label {
            font-weight: 600;
            color: var(--color-gray-600);
            margin-right: 6px;
        }

        /* Chips */
        .chip {
            display: inline-block;
            background: #EBF7FF;
            border-radius: 16px;
            padding: 4px 12px;
            font-size: 13px;
            color: #858e95;
        }






        /* Score Display Styles */
        .score-display-container {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 20px;
            padding: 24px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }

        .score-item {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .score-label {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .score-value-display {
            font-size: 36px;
            font-weight: 700;
            /* line-height: 1; */
            padding: 12px 20px;
            border-radius: 12px;
            min-width: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .score-value-display.current-score {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }

        .score-value-display.max-score {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .score-separator {
            font-size: 42px;
            font-weight: 300;
            color: #94a3b8;
            margin: 0 10px;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }
    </style>
    <style>
        @media (max-width: 639px) {


            .card {
                padding: 16px;
                margin-bottom: 12px;
            }

            .indicator-title {
                font-size: 20px;
                /* line-height: 1.3; */
            }

            .indicator-tabs {
                flex-direction: column;
                gap: 3px;
                align-items: flex-start;
            }

            .tab-for-divider {
                display: none;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .chip {
                font-size: 12px;
                padding: 3px 8px;
            }

            .criteria-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .criteria-title {
                font-size: 13px;
            }

            .evidence-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                padding: 8px;
            }

            .action-bts {
                flex-direction: column;
                gap: 8px;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .score-display-container {
                flex-direction: column;
                gap: 16px;
                padding: 20px 16px;
                border-radius: 12px;
            }

            .score-item {
                text-align: center;
                padding: 12px;
                background: rgba(255, 255, 255, 0.8);
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                width: 100%;
            }

            .score-label {
                font-size: 12px;
                margin-bottom: 8px;
            }

            .score-separator {
                display: none;
            }

            .score-value-display {
                font-size: 28px;
                padding: 12px 20px;
                min-width: 80px;
                margin: 0 auto;
            }

            .variable-row {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                text-align: left;
            }

            .variable-display {
                width: 100%;
                text-align: left;
            }
        }

        @media (min-width: 640px) and (max-width: 767px) {

            .card {
                padding: 20px;
            }

            .indicator-title {
                font-size: 22px;
            }

            .criteria-header {
                flex-wrap: wrap;
                flex-direction: column;
                gap: 8px;
            }

            .evidence-item {
                flex-wrap: wrap;
                gap: 8px;
            }

            .action-bts {
                flex-wrap: wrap;
                gap: 8px;
            }

            .score-display-container {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
                gap: 20px;
                padding: 24px 20px;
            }

            .score-item {
                flex: 1;
                min-width: 120px;
                max-width: 200px;
            }

            .score-separator {
                align-self: center;
                font-size: 36px;
                margin: 0 8px;
            }

            .score-value-display {
                font-size: 30px;
                padding: 12px 18px;
                min-width: 70px;
            }

            .variable-row {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .variable-display {
                width: 100%;
            }


        }

        @media (min-width: 768px) and (max-width: 1023px) {

            .card {
                padding: 22px;
            }

            .indicator-title {
                font-size: 24px;
            }

            .criteria-header {
                gap: 12px;
            }

            .score-display-container {
                gap: 18px;
                padding: 22px;
            }

            .score-value-display {
                font-size: 32px;
                padding: 10px 18px;
                min-width: 70px;
            }

            .variable-display {
                width: 250px;
            }

            .action-bts {
                gap: 10px;
            }

            .evidence-item {
                padding: 8px 12px;
            }
        }

        @media (min-width: 1024px) and (max-width: 1279px) {

            .card {
                padding: 24px;
            }

            .indicator-title {
                font-size: 25px;
            }

            .score-display-container {
                gap: 20px;
                padding: 24px;
            }

            .score-value-display {
                font-size: 34px;
                padding: 11px 19px;
                min-width: 75px;
            }

            .variable-display {
                width: 280px;
            }

            .action-bts {
                gap: 12px;
            }
        }

        @media (min-width: 1280px) and (max-width: 1535px) {

            .card {
                padding: 24px;
            }

            .indicator-title {
                font-size: 26px;
            }

            .score-display-container {
                gap: 20px;
                padding: 24px;
            }

            .score-value-display {
                font-size: 36px;
                padding: 12px 20px;
                min-width: 80px;
            }

            .variable-display {
                width: 300px;
            }

            .action-bts {
                gap: 12px;
            }
        }
    </style>
@endpush
