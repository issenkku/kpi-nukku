@extends('layouts.app')

@section('title', $indicator->code . ' : ' . $indicator->name)

@section('content')

    @php
        $locked = in_array($indicator->status, [3, 4]);
    @endphp
    <div class="indicator-card">
        <div class="indicator-header-container">
            <h1 class="indicator-title">
                {{ $indicator->name }} ({{ $indicator->code }})
            </h1>
            <div class="indicator-tabs">
                <span class="tab ">{{ $indicator->category->standard->name ?? '-' }}</span>
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
        <div class="card ">
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
                        {{-- <div class="criteria-status"> --}}
                        <label for="criteria-status-{{ $criteria->id }}" class="sr-only">สถานะเกณฑ์</label>
                        <select id="criteria-status-{{ $criteria->id }}" name="criterias[{{ $criteria->id }}][status]" class="criteria-status text-sm text-center"
                            @if ($locked) disabled @endif form="variables-form"
                            data-criteria-id="{{ $criteria->id }}">
                            <option value="0" {{ ($criteria->status ?? 0) == 0 ? 'selected' : '' }}>
                                รอดำเนินการ</option>
                            <option value="1" {{ ($criteria->status ?? 0) == 1 ? 'selected' : '' }}>
                                เอกสารครบถ้วน
                            </option>
                            <option value="2" {{ ($criteria->status ?? 0) == 2 ? 'selected' : '' }}>
                                เอกสารไม่ครบถ้วน
                            </option>
                        </select>
                        {{-- </div> --}}
                    </div>
                    <div class="criteria-content">
                        {{-- อัปโหลดหลักฐาน --}}
                        <x-evidence-uploader :criteria="$criteria" :store-route="route('evidences.store')" :locked-statuses="$locked" />
                        {{-- คำอธิบายเกณฑ์ --}}
                        @if ($criteria->description)
                            <div class="criteria-description">
                                {!! $criteria->description !!}
                            </div>
                        @endif
                        @php
                            // Pick the most recent non-empty detail from evidences of this criteria
                            $detailEvidence = $criteria->evidences
                                ->sortByDesc(function($e){ return $e->created_at; })
                                ->first(function($e){ return filled($e->detail); });
                            $detailId = $detailEvidence?->id;
                            $detailHtml = $detailEvidence->detail ?? ($criteria->report ?? '');
                        @endphp

                        <div class="criteria-detail mb-3"
                             data-criteria-id="{{ $criteria->id }}"
                             data-evidence-id="{{ $detailId }}"
                             data-store-url="{{ route('evidences.store') }}"
                             @if($detailId) data-update-url="{{ route('evidences.update', $detailId) }}" @endif>
                            <div class="flex items-center justify-between mb-1">
                                <div class="font-semibold text-gray-800">รายงานผลการดำเนินงาน</div>
                                @if (!$locked)
                                    <div class="flex gap-2 text-sm">
                                        <button type="button" class="btn btn-xs btn-outline detail-edit-btn">แก้ไข</button>
                                        <button type="button" class="btn btn-xs btn-primary detail-save-btn" style="display:none">บันทึก</button>
                                        <button type="button" class="btn btn-xs btn-outline detail-cancel-btn" style="display:none">ยกเลิก</button>
                                    </div>
                                @endif
                            </div>
                            <div class="prose max-w-none text-sm text-gray-800 break-words criteria-detail-view">
                                {!! $detailHtml !!}
                            </div>
                            @if (!$locked)
                                <textarea id="detailEditor-{{ $criteria->id }}" class="criteria-detail-editor eu-editor" rows="6" style="display:none">{!! $detailHtml !!}</textarea>
                            @endif
                        </div>

                        <div class="evidence-list evidence-list-{{ $criteria->id }}">
                            @forelse($criteria->evidences as $evidence)
                                @php
                                    $type = strtolower($evidence->type ?? '');
                                    $name = strtolower($evidence->name ?? '');
                                @endphp
                                <div class="evidence-item" id="evidence-{{ $evidence->id }}" data-evidence-id="{{ $evidence->id }}">
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
                                        @if (!$locked)
                                            <button type="button"
                                                class=" text-slate-500 hover:text-slate-700 cursor-pointer"
                                                title="แก้ไขชื่อไฟล์" onclick="startEditEvidenceName({{ $evidence->id }})">
                                                ✏️
                                            </button>
                                            <span id="evidence-edit-{{ $evidence->id }}"
                                                class="evidence-edit flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto"
                                                style="display:none"
                                                data-update-url="{{ route('evidences.update', $evidence->id) }}">
                                                <input type="text" id="evidence-input-{{ $evidence->id }}" aria-label="ชื่อไฟล์หลักฐาน"
                                                    value="{{ $evidence->name }}"
                                                    class="border rounded px-2 py-1 text-[13px] min-w-0 w-full sm:w-60" />
                                                <div class="button-group flex space-x-2">
                                                    <button type="button"
                                                        class="text-green-600 hover:text-green-700 cursor-pointer "
                                                        onclick="saveEvidenceName({{ $evidence->id }})">บันทึก</button>
                                                    <button type="button"
                                                        class="text-slate-600 hover:text-slate-800 cursor-pointer"
                                                        onclick="cancelEditEvidenceName({{ $evidence->id }})">ยกเลิก</button>
                                                </div>
                                            </span>
                                        @endif

                                    </div>

                                    {{-- Detail shown once above per criteria --}}

                                    <div class="flex items-center justify-center">
                                        {{-- Modal ลบหลักฐาน --}}
                                        <x-modal title="ยืนยันการลบหลักฐาน" size="md" :context="'delete-evidence-' . $evidence->id"
                                            :closeOnBg="false">
                                            <x-slot:trigger>
                                                <button type="button" class="btn-delete" title="ลบหลักฐาน"
                                                    @if ($locked) hidden @endif>
                                                    ลบ
                                                </button>
                                            </x-slot:trigger>

                                            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                <p class="text-sm text-yellow-800">
                                                    <strong>คำเตือน:</strong> การลบหลักฐานนี้จะไม่สามารถกู้คืนได้
                                                </p>
                                            </div>

                                            <div class="mb-6 text-center text-gray-700 text-sm">
                                                <p>คุณต้องการลบหลักฐาน </p>
                                                <p>"<span
                                                        class="font-semibold text-red-600 text-pretty">{{ $evidence->name }}</span>
                                                "</p>
                                                <p>หรือไม่?</p>
                                            </div>

                                            <form x-ref="delForm" method="POST"
                                                action="{{ route('evidences.destroy', $evidence->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="redirect" value="{{ url()->current() }}">

                                                <div class="flex gap-2 justify-between">
                                                    <button type="button" class="btn btn-outline"
                                                        @click="$dispatch('modal:close')">
                                                        <i class="fa fa-undo"></i>ยกเลิก
                                                    </button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fa fa-trash"></i>ยืนยันการลบ
                                                    </button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-center text-gray-500 opacity-75">----- ยังไม่มีหลักฐานแนบ
                                    -----
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center">----- ยังไม่มีเกณฑ์การพิจารณา -----</p>
            @endforelse
        </div>
        @php
            $condition = $indicator->condition ?? '';
            // ลบช่องว่างรอบ ๆ
            $trimmed = trim($condition);

            // เช็คว่ามีแท็ก <img> หรือมีข้อความจริง ๆ หลังจากลบแท็ก HTML
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
                <h2 class="card-title">กรอกค่าตัวแปร</h2>
                @php
                    $inputVariables = $indicator->variables->filter(fn($v) => trim($v->type) === 'input');
                @endphp
                @forelse($inputVariables as $variable)
                    <div class="variable-row">
                        <label class="variable-label" for="variable-{{ $variable->id }}">
                            {{ $variable->label_name ?? $variable->variable_name }}
                        </label>
                        <input type="number" step="0.01" inputmode="decimal" id="variable-{{ $variable->id }}" name="variables[{{ $variable->id }}]"
                            value="{{ old('variables.' . $variable->id, $variable->value) }}"
                            placeholder="กรุณากรอกร้อยละเป็นตัวเลข" class="variable-input" form="variables-form"
                            @if ($locked) readonly @endif>
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

        <form id="variables-form" action="{{ route('dashboardkpi.admin.saveVariables', $indicator->id) }}"
            method="POST" class="hidden">
            @csrf
            @method('PUT')
            <!-- ✅ hidden status -->
            <input type="hidden" name="status" id="status-input" value="{{ $indicator->status ?? 2 }}">
        </form>

        <div class="action-bts">
            <button type="button" class="btn btn-outline" id="back-btn"
                onclick="location.href='{{ route('dashboardkpi.index') }}'">
                <i class="fa fa-undo"></i> กลับ
            </button>

            <button type="submit" class="btn btn-primary" form="variables-form"
                @if ($locked) hidden @endif>
                <i class="fa fa-save"></i> บันทึกผลลัพธ์
            </button>

            <x-modal title="เปลี่ยนสถานะตัวบ่งชี้" size="sm" :context="'status'">
                <x-slot:trigger>
                    <button type="button" class="btn btn-secondary" data-allow-when-locked="true">
                        <i class="fa-solid fa-gear"></i>เปลี่ยนสถานะตัวบ่งชี้
                    </button>
                </x-slot:trigger>

                <div class="space-y-2">
                    <button type="button" class="status-choice w-full text-left px-4 py-2 rounded hover:bg-slate-50"
                        data-status="1">
                        1 — บันทึกเป็นฉบับร่าง
                    </button>
                    <button type="button" class="status-choice w-full text-left px-4 py-2 rounded hover:bg-slate-50"
                        data-status="2">
                        2 — บันทึกเป็นฉบับจริง
                    </button>
                    <hr class="my-1 border-slate-200">
                    <button type="button" class="status-choice w-full text-left px-4 py-2 rounded hover:bg-slate-50"
                        data-status="3">
                        3 — ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรฐาน
                    </button>
                    <button type="button" class="status-choice w-full text-left px-4 py-2 rounded hover:bg-slate-50"
                        data-status="4">
                        4 — ผลการดำเนินงานไม่ครบถ้วนตามเกณฑ์มาตรฐาน
                    </button>
                </div>

                <x-slot:footer>
                    <div class="flex justify-end">
                        <button type="button" class="btn btn-ghost" @click="$dispatch('modal:close')">
                            ปิด
                        </button>
                    </div>
                </x-slot:footer>
            </x-modal>


            <form id="notify-form" action="{{ route('notify', $indicator->id) }}" method="POST">
                @csrf

                <!-- ✅ hidden status -->

                <button type="submit" class="btn btn-warning" form="notify-form"
                    @if ($indicator->status === 2 || $locked) disabled @endif>
                    <i class="fa-solid fa-bell"></i>
                    <span class="hidden sm:inline">แจ้งเตือนผู้รับผิดชอบ</span>
                    <span class="sm:hidden">แจ้งเตือน</span>
                </button>
            </form>

        </div>
    </div>
@endsection

@push('scripts')
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600&family=Kanit:wght@400;600&family=Sarabun:wght@400;600&display=swap"
        rel="stylesheet">

    <!-- ✅ โหลด jQuery + Trumbowyg -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/langs/th.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/colors/ui/trumbowyg.colors.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/colors/trumbowyg.colors.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/fontsize/trumbowyg.fontsize.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/fontfamily/trumbowyg.fontfamily.min.js">
    </script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/table/ui/trumbowyg.table.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/table/trumbowyg.table.min.js"></script>

    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script>
        // When clicking an evidence row, show its detail in the panel of the same criteria box
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.evidence-list .evidence-item').forEach(item => {
                item.addEventListener('click', async function (e) {
                    // ignore interactive targets
                    if (e.target.closest('a, button, .btn-delete, input, textarea, label, [role="button"]')) return;
                    const id = this.getAttribute('data-evidence-id') || (this.id.match(/evidence-(\d+)/)?.[1] ?? '');
                    if (!id) return;
                    try {
                        const resp = await fetch(`/evidences/${id}`, { headers: { 'Accept': 'application/json' } });
                        const raw = await resp.text();
                        let data = null; try { data = JSON.parse(raw); } catch (_) {}
                        if (!resp.ok || !data || data.success === false) return;

                        const detail = data.data?.detail || '';
                        const criteriaBox = this.closest('.criteria-box');
                        if (!criteriaBox) return;
                        const detailSection = criteriaBox.querySelector('.criteria-detail');
                        const view = criteriaBox.querySelector('.criteria-detail-view');
                        const editor = criteriaBox.querySelector('.criteria-detail-editor');
                        if (!detailSection || !view) return;

                        detailSection.setAttribute('data-evidence-id', String(id));
                        detailSection.setAttribute('data-update-url', `/evidences/${id}`);
                        view.innerHTML = detail;
                        if (editor) {
                            try {
                                if (window.$ && typeof $.fn.trumbowyg === 'function' && $(editor).data('trumbowyg')) {
                                    $(editor).trumbowyg('html', detail);
                                } else if (editor.tagName === 'TEXTAREA') {
                                    editor.value = detail;
                                } else {
                                    editor.innerHTML = detail;
                                }
                            } catch (_) {}
                        }

                        // Ensure view mode
                        const editBtn = criteriaBox.querySelector('.detail-edit-btn');
                        const saveBtn = criteriaBox.querySelector('.detail-save-btn');
                        const cancelBtn = criteriaBox.querySelector('.detail-cancel-btn');
                        view.style.display = '';
                        if (editor) {
                            editor.style.display = 'none';
                            const editorBox = criteriaBox.querySelector('.trumbowyg-box');
                            if (editorBox) editorBox.style.display = 'none';
                        }
                        if (editBtn) editBtn.style.display = '';
                        if (saveBtn) saveBtn.style.display = 'none';
                        if (cancelBtn) cancelBtn.style.display = 'none';

                        detailSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } catch (err) {
                        // ignore fetch errors
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("variables-form");
            const statusInput = document.getElementById("status-input");

            // Add event listeners to all buttons with the class #save-results-btn
            document.querySelectorAll("#save-results-btn").forEach(btn => {
                btn.addEventListener("click", function() {
                    // Get the status from the button's data-status attribute
                    const status = this.getAttribute("data-status") || statusInput.value;

                    // Set the hidden input value to the status
                    statusInput.value = status;

                    // Submit the form
                    form.submit();
                });
            });

            // Add click event listeners to all buttons with the class "status-choice"
            document.querySelectorAll('.status-choice').forEach(button => {
                button.addEventListener('click', function() {
                    const status = this.getAttribute('data-status'); // Get the data-status value

                    // Set the status value in the hidden input field
                    statusInput.value = status;

                    // Submit the form
                    form.submit();
                });
            });

            // จัดการการเปลี่ยนสีของ criteria status
            function updateCriteriaStatusStyle() {
                document.querySelectorAll('.criteria-status').forEach(select => {
                    const value = select.value;

                    // ลบ class เดิมทั้งหมด
                    select.classList.remove('status-pending', 'status-completed', 'status-rejected');

                    // เพิ่ม class ใหม่ตามค่าที่เลือก
                    if (value === '0') {
                        select.classList.add('status-pending');
                    } else if (value === '1') {
                        select.classList.add('status-completed');
                    } else if (value === '2') {
                        select.classList.add('status-rejected');
                    }
                });
            }

            // เรียกใช้เมื่อโหลดหน้าเว็บ
            updateCriteriaStatusStyle();

            // เพิ่ม event listener สำหรับการเปลี่ยนค่า
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('criteria-status')) {
                    updateCriteriaStatusStyle();
                }
            });
        });
    </script>
    <script>
        // กัน error ถ้าไม่ได้โหลด lucide
        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }

        // ✅ Template render หลักฐานใหม่ ให้เหมือน Blade
        function renderEvidenceItem(ev) {
            let icon = '<i data-lucide="file" style="color:#6b7280;"></i>'; // default
            if (ev.type === 'pdf') {
                icon = '<i data-lucide="file-text" style="color:#dc2626;"></i>';
            } else if (['doc', 'docx'].includes(ev.type)) {
                icon = '<i data-lucide="file-text" style="color:#2563eb;"></i>';
            } else if (['ppt', 'pptx'].includes(ev.type)) {
                icon = '<i data-lucide="presentation" style="color:#eb7e25;"></i>';
            } else if (['jpg', 'jpeg', 'png', 'gif', 'image'].includes(ev.type)) {
                icon = '<i data-lucide="image" style="color:#16a34a;"></i>';
            } else if (ev.type === 'xls' || ev.type === 'xlsx') {
                icon = '<i data-lucide="file-spreadsheet" style="color:#059669;"></i>';
            } else if (ev.type === 'url') {
                icon = '<i data-lucide="link" style="color:#9333ea;"></i>';
            } else if (ev.type === 'note') {
                icon = '<i data-lucide="sticky-note" style="color:#f59e0b;"></i>';
            }

            let nameHtml = ev.name;
            if (ev.type === 'url' && ev.path?.urls?.[0]) {
                nameHtml = `<a href="${ev.path.urls[0]}" target="_blank"
                      class="text-blue-600 underline hover:text-blue-800">
                        ${ev.name}
                    </a>`;
            }

            return `
        <div class="evidence-item" id="evidence-${ev.id}">
            <span class="evidence-icon">${icon}</span>
            <span class="evidence-name">${nameHtml}</span>
            <button class="btn-delete" data-id="${ev.id}" title="ลบหลักฐาน">x</button>
        </div>`;
        }


        document.addEventListener('DOMContentLoaded', function() {
            const fileHandlers = {};
            const editorInitialized = {};
            /*** ---------- File Upload Handler Class ---------- ***/
            class FileUploadHandler {
                constructor(criteriaId) {
                    this.criteriaId = criteriaId;
                    this.uploadArea = document.querySelector(`.upload-area-${criteriaId}`);
                    this.fileInput = document.getElementById(`fileInput-${criteriaId}`);
                    this.filesList = document.getElementById(`filesList-${criteriaId}`);
                    this.selectedFiles = [];
                    fileHandlers[criteriaId] = this;
                    this.init();
                }
                init() {
                    if (!this.uploadArea || !this.fileInput || !this.filesList) return;
                    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evt => {
                        this.uploadArea.addEventListener(evt, e => {
                            e.preventDefault();
                            e.stopPropagation();
                        });
                    });
                    this.uploadArea.addEventListener('click', () => this.fileInput.click());
                    this.uploadArea.addEventListener('dragover', e => {
                        e.preventDefault();
                        this.uploadArea.classList.add('drag-over');
                    });
                    this.uploadArea.addEventListener('dragleave', () => {
                        this.uploadArea.classList.remove('drag-over');
                    });
                    this.uploadArea.addEventListener('drop', e => {
                        e.preventDefault();
                        this.uploadArea.classList.remove('drag-over');
                        this.handleFiles(Array.from(e.dataTransfer.files || []));
                    });
                    this.fileInput.addEventListener('change', e => {
                        this.handleFiles(Array.from(e.target.files || []));
                    });
                }
                handleFiles(files) {
                    files.forEach(file => {
                        const exists = this.selectedFiles.find(f =>
                            f.name === file.name && f.size === file.size && f.type === file.type
                        );
                        if (!exists) {
                            this.selectedFiles.push(file);
                            this.displayFile(file);
                        }
                    });
                    this.syncInputFiles();
                }
                displayFile(file) {
                    const el = document.createElement('div');
                    el.className = 'file-item';
                    el.innerHTML = `
                    <div class="file-icon">
                        <i data-lucide="file-text" style="width:20px;height:20px;color:#ef4444;"></i>
                    </div>
                    <span class="file-name" title="${file.name}">${file.name}</span>
                    <span class="file-size">${this.formatFileSize(file.size)}</span>
                    <button type="button" class="remove-file" aria-label="ลบไฟล์"
                        data-name="${encodeURIComponent(file.name)}" 
                        data-size="${file.size}"
                        data-criteria="${this.criteriaId}">
                        <i data-lucide="x" style="width:16px;height:16px;"></i>
                    </button>`;
                    this.filesList.appendChild(el);
                    el.querySelector('.remove-file').addEventListener('click', e => {
                        const btn = e.currentTarget;
                        const name = decodeURIComponent(btn.getAttribute('data-name') || '');
                        const size = Number(btn.getAttribute('data-size') || 0);
                        const criteriaId = btn.getAttribute('data-criteria');
                        const handler = fileHandlers[criteriaId];
                        if (handler) {
                            handler.selectedFiles = handler.selectedFiles.filter(f =>
                                !(f.name === name && f.size === size)
                            );
                            handler.syncInputFiles();
                        }
                        el.remove();
                    });
                    if (window.lucide?.createIcons) lucide.createIcons();
                }
                syncInputFiles() {
                    if (!this.fileInput) return;
                    const dt = new DataTransfer();
                    this.selectedFiles.forEach(file => dt.items.add(file));
                    this.fileInput.files = dt.files;
                }
                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024,
                        sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }
            }

            /*** ---------- URL Handler Class ---------- ***/
            class URLHandler {
                constructor(criteriaId) {
                    this.criteriaId = criteriaId;
                    this.urlSection = document.querySelector(`.url-section-${criteriaId}`);
                    this.init();
                }
                init() {
                    if (!this.urlSection) return;
                    if (!this.urlSection.querySelector('input[type="url"]:not([readonly])')) {
                        this.appendNewEditableRow();
                    }
                    this.urlSection.addEventListener('click', e => this.handleClick(e));
                    this.urlSection.addEventListener('keydown', e => this.handleKeydown(e));
                }
                handleClick(e) {
                    const addBtn = e.target.closest(`.add-url-btn-${this.criteriaId}`);
                    if (addBtn) {
                        const row = addBtn.closest('.url-row');
                        this.lockRowAndSwapButton(row);
                        this.appendNewEditableRow();
                        return;
                    }
                    const removeBtn = e.target.closest('.remove-url-btn');
                    if (removeBtn && removeBtn.closest(`.url-section-${this.criteriaId}`)) {
                        const row = removeBtn.closest('.url-row');
                        row?.remove();
                        if (!this.urlSection.querySelector('input[type="url"]:not([readonly])')) {
                            this.appendNewEditableRow();
                        }
                    }
                }
                handleKeydown(e) {
                    if (e.key === 'Enter' && e.target.matches(`.url-input-${this.criteriaId}`)) {
                        e.preventDefault();
                        const row = e.target.closest('.url-row');
                        this.lockRowAndSwapButton(row);
                        this.appendNewEditableRow();
                    }
                }
                appendNewEditableRow() {
                    const row = document.createElement('div');
                    row.className = `form-group url-row`;
                    row.innerHTML = `
                    <input type="text" name="url_names[]" aria-label="ชื่อ URL" 
                        class="form-input url-name url-name-${this.criteriaId}" 
                        placeholder="ชื่อหลักฐาน URL">
                    <input type="url" name="additional_urls[]" aria-label="ที่อยู่ URL" 
                        class="form-input url-input url-input-${this.criteriaId}" 
                        placeholder="วาง URL เพิ่มเติม">
                    <button type="button" 
                        class="add-url-btn add-url-btn-${this.criteriaId}" 
                        aria-label="เพิ่ม URL">
                        <i data-lucide="plus" style="width:16px;height:16px;"></i>
                    </button>`;
                    this.urlSection.appendChild(row);
                    if (window.lucide?.createIcons) lucide.createIcons();
                    row.querySelector(`input.url-input-${this.criteriaId}`)?.focus();
                }
                lockRowAndSwapButton(row) {
                    if (!row) return;
                    const input = row.querySelector(`input.url-input-${this.criteriaId}`);
                    if (!input || !input.value.trim()) return;
                    input.readOnly = true;
                    input.classList.add('locked');
                    input.setAttribute('tabindex', '-1');
                    const addBtn = row.querySelector(`.add-url-btn-${this.criteriaId}`);
                    if (addBtn) {
                        addBtn.classList.remove(`add-url-btn-${this.criteriaId}`);
                        addBtn.classList.add('remove-url-btn');
                        addBtn.setAttribute('aria-label', 'ลบ URL');
                        addBtn.innerHTML = `<i data-lucide="x" style="width:16px;height:16px;"></i>`;
                        if (window.lucide?.createIcons) lucide.createIcons();
                    }
                }
            }

            /*** ---------- Trumbowyg Editor ---------- ***/
            let lastActiveEditor = null;
            let lastActiveTextarea = null;
            function insertImageIntoTextarea(textarea, dataUrl) {
                const html = `<img src="${dataUrl}">`;
                const start = textarea.selectionStart ?? textarea.value.length;
                const end = textarea.selectionEnd ?? textarea.value.length;
                textarea.value = textarea.value.slice(0, start) + html + textarea.value.slice(end);
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
                try {
                    textarea.selectionStart = textarea.selectionEnd = start + html.length;
                } catch (_) {}
            }

            function insertHtmlIntoTextarea(textarea, html) {
                const start = textarea.selectionStart ?? textarea.value.length;
                const end = textarea.selectionEnd ?? textarea.value.length;
                textarea.value = textarea.value.slice(0, start) + html + textarea.value.slice(end);
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
                try {
                    textarea.selectionStart = textarea.selectionEnd = start + html.length;
                } catch (_) {}
            }

            function tabTextToTableHtml(text) {
                const rows = String(text || '').trim().split(/\r?\n/).filter(r => r.length);
                if (!rows.length) return '';
                const pickDelimiter = (lines) => {
                    const hasTab = lines.some(r => r.includes('\t'));
                    if (hasTab) return '\t';
                    const candidates = [',', ';', '|'];
                    let best = null;
                    for (const d of candidates) {
                        const counts = lines.map(r => r.split(d).length);
                        const max = Math.max(...counts);
                        if (max < 2) continue;
                        const same = counts.filter(c => c === max).length;
                        if (!best || (same > best.same) || (same === best.same && max > best.max)) {
                            best = { d, max, same };
                        }
                    }
                    if (best) return best.d;
                    // fallback: split on 2+ spaces
                    const spaceCounts = lines.map(r => r.split(/\s{2,}/).length);
                    const spaceMax = Math.max(...spaceCounts);
                    return spaceMax >= 2 ? /\s{2,}/ : null;
                };

                const delimiter = pickDelimiter(rows);
                if (!delimiter) return '';
                const splitter = delimiter instanceof RegExp ? delimiter : delimiter;
                const matrix = rows.map(r => r.split(splitter));
                const hasTable = matrix.length > 1 || matrix.some(r => r.length > 1);
                if (!hasTable) return '';
                const escapeHtml = (s) => String(s)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
                const body = matrix.map(cols => {
                    const tds = cols.map(c => `<td>${escapeHtml(c)}</td>`).join('');
                    return `<tr>${tds}</tr>`;
                }).join('');
                return `<table><tbody>${body}</tbody></table>`;
            }

            function extractTableHtmlFromClipboard(evt) {
                const html = evt?.clipboardData?.getData?.('text/html') || '';
                if (html) {
                    const match = html.match(/<table[\s\S]*<\/table>/i);
                    if (match) return match[0];
                }
                const text = evt?.clipboardData?.getData?.('text/plain') || '';
                return tabTextToTableHtml(text);
            }
            window.extractTableHtmlFromClipboard = extractTableHtmlFromClipboard;

            function handleTabularPaste(evt, insertHtml) {
                const tableHtml = extractTableHtmlFromClipboard(evt);
                if (!tableHtml) return false;
                evt.preventDefault();
                insertHtml(tableHtml);
                return true;
            }

            function attachPasteImage($editor, textareaEl) {
                if ($editor && $editor.length) {
                    const $box = $editor.closest('.trumbowyg-box');
                    const $ed = $box.find('.trumbowyg-editor');
                    if ($ed.length) {
                        $ed.off('focus.pasteImageTracker').on('focus.pasteImageTracker', function() {
                            lastActiveEditor = $editor;
                            lastActiveTextarea = null;
                        });
                    }
                    if ($ed.length && !$ed.data('pasteImageBound')) {
                        $ed.data('pasteImageBound', true);
                        $ed.on('paste', function(e) {
                            const evt = e.originalEvent || e;
                            if (handleTabularPaste(evt, (html) => {
                                    try { $editor.trumbowyg('execCmd', { cmd: 'insertHTML', param: html }); } catch (_) {}
                                })) {
                                return;
                            }
                            const items = evt?.clipboardData?.items || [];
                            for (const item of items) {
                                if (item.type && item.type.indexOf('image') === 0) {
                                    e.preventDefault();
                                    const file = item.getAsFile();
                                    if (!file) return;
                                    const reader = new FileReader();
                                    reader.onload = function(ev) {
                                        try {
                                            $editor.trumbowyg('execCmd', {
                                                cmd: 'insertHTML',
                                                param: `<img src="${ev.target.result}">`
                                            });
                                        } catch (_) {}
                                    };
                                    reader.readAsDataURL(file);
                                    break;
                                }
                            }
                        });
                        $ed.on('dragover drop', function(e) {
                            e.preventDefault();
                        });
                        $ed.on('drop', function(e) {
                            const evt = e.originalEvent || e;
                            const files = Array.from(evt?.dataTransfer?.files || []);
                            const imageFile = files.find(f => f.type && f.type.indexOf('image') === 0);
                            if (!imageFile) return;
                            const reader = new FileReader();
                            reader.onload = function(ev) {
                                try {
                                    $editor.trumbowyg('execCmd', {
                                        cmd: 'insertHTML',
                                        param: `<img src="${ev.target.result}">`
                                    });
                                } catch (_) {}
                            };
                            reader.readAsDataURL(imageFile);
                        });
                    }
                }

                if (textareaEl && !textareaEl.dataset.pasteImageBound) {
                    textareaEl.dataset.pasteImageBound = '1';
                    textareaEl.addEventListener('focus', function() {
                        lastActiveTextarea = textareaEl;
                        lastActiveEditor = null;
                    });
                    textareaEl.addEventListener('paste', function(e) {
                        if (handleTabularPaste(e, (html) => insertHtmlIntoTextarea(textareaEl, html))) return;
                        const items = e.clipboardData?.items || [];
                        for (const item of items) {
                            if (item.type && item.type.indexOf('image') === 0) {
                                e.preventDefault();
                                const file = item.getAsFile();
                                if (!file) return;
                                const reader = new FileReader();
                                reader.onload = function(ev) {
                                    insertImageIntoTextarea(textareaEl, ev.target.result);
                                };
                                reader.readAsDataURL(file);
                                break;
                            }
                        }
                    });
                    textareaEl.addEventListener('dragover', function(e) {
                        e.preventDefault();
                    });
                    textareaEl.addEventListener('drop', function(e) {
                        const files = Array.from(e.dataTransfer?.files || []);
                        const imageFile = files.find(f => f.type && f.type.indexOf('image') === 0);
                        if (!imageFile) return;
                        e.preventDefault();
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            insertImageIntoTextarea(textareaEl, ev.target.result);
                        };
                        reader.readAsDataURL(imageFile);
                    });
                }
            }

            function initTrumbowyg(criteriaId) {
                if (typeof $ === 'undefined' || typeof $.fn.trumbowyg === 'undefined') return false;
                if (editorInitialized[criteriaId]) return true;
                const selector = `#detailEditor-${criteriaId}`;
                const $editor = $(selector);
                if ($editor.length === 0) return false;
                try {
                    if ($editor.data('trumbowyg')) $editor.trumbowyg('destroy');
                    $editor.trumbowyg({
                        lang: 'th',
                        btns: [
                            ['viewHTML'],
                            ['undo', 'redo'],
                            ['formatting'],
                            ['fontfamily'],
                            ['fontsize'],
                            ['foreColor', 'backColor'],
                            ['strong', 'em', 'del', 'underline'],
                            ['link'],
                            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                            ['unorderedList', 'orderedList'],
                            ['table'],
                            ['horizontalRule'],
                            ['removeformat'],
                            ['fullscreen']
                        ],
                        plugins: {
                            fontfamily: {
                                fonts: [
                                    'Prompt, sans-serif', 'Kanit, sans-serif',
                                    'Sarabun, sans-serif', 'Arial, sans-serif',
                                    'Times New Roman, serif'
                                ]
                            },
                            fontsize: {
                                allowCustomSize: true,
                                sizeList: ['12px', '14px', '16px', '18px', '20px', '24px', '28px', '32px']
                            }
                        },
                        autogrow: true
                    });
                    attachPasteImage($editor, $editor[0]);
                    editorInitialized[criteriaId] = true;
                    return true;
                } catch (error) {
                    console.error(`❌ Error initializing Trumbowyg:`, error);
                    return false;
                }
            }

            function setupPopupTriggers() {
                @foreach ($indicator->criterias as $criteria)
                    const btn{{ $criteria->id }} = document.querySelector(
                        '[\\@click="open{{ $criteria->id }} = true"]');
                    if (btn{{ $criteria->id }}) {
                        btn{{ $criteria->id }}.addEventListener("click", () => {
                            setTimeout(() => initTrumbowyg({{ $criteria->id }}), 600);
                        });
                    }
                @endforeach
            }
            setupPopupTriggers();

            function observePopupVisibility(criteriaId) {
                const popup = document.querySelector(`[x-show="open${criteriaId}"]`);
                if (!popup) return;
                const observer = new MutationObserver(() => {
                    const isVisible = !popup.hasAttribute('x-cloak') &&
                        popup.style.display !== 'none' &&
                        popup.offsetParent !== null;
                    if (isVisible && !editorInitialized[criteriaId]) {
                        setTimeout(() => initTrumbowyg(criteriaId), 100);
                    }
                });
                observer.observe(popup, {
                    attributes: true,
                    attributeFilter: ['style', 'class', 'x-cloak']
                });
            }

            @foreach ($indicator->criterias as $criteria)
                new FileUploadHandler({{ $criteria->id }});
                new URLHandler({{ $criteria->id }});
                observePopupVisibility({{ $criteria->id }});
            @endforeach
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            document.querySelectorAll('.criteria-detail').forEach(section => {
                const view = section.querySelector('.criteria-detail-view');
                const editor = section.querySelector('.criteria-detail-editor');
                const editBtn = section.querySelector('.detail-edit-btn');
                const saveBtn = section.querySelector('.detail-save-btn');
                const cancelBtn = section.querySelector('.detail-cancel-btn');
                const criteriaId = section.getAttribute('data-criteria-id');
                const storeUrl = section.getAttribute('data-store-url');

                if (!editBtn || !editor) return; // locked or missing

                let trumboReady = false;
                let editorBox = null; // Trumbowyg wrapper
                // If editor was auto-initialized elsewhere, ensure it is hidden in view-mode initially
                try {
                    editorBox = section.querySelector('.trumbowyg-box');
                    const hasDetail = (section.getAttribute('data-evidence-id') || '').length > 0;
                    if (editorBox && hasDetail) {
                        editorBox.style.display = 'none';
                    }
                } catch (e) {}
                const ensureEditor = () => {
                    if (trumboReady) return;
                    try {
                        if (window.$ && typeof $.fn.trumbowyg === 'function') {
                            $(editor).trumbowyg({
                                lang: 'th',
                                resetCss: true,
                                removeformatPasted: true,
                                btns: [
                                    ['viewHTML'], ['undo', 'redo'], ['formatting'], ['strong', 'em', 'del'],
                                    ['fontsize', 'foreColor'], ['link'], ['unorderedList', 'orderedList'], ['table'],
                                    ['justifyLeft','justifyCenter','justifyRight','justifyFull'], ['horizontalRule'], ['removeformat']
                                ]
                            });
                            // cache wrapper box for show/hide
                            editorBox = section.querySelector('.trumbowyg-box');
                            attachPasteImage($(editor), editor);
                            trumboReady = true;
                        }
                    } catch (e) {}
                };

                const toEditMode = () => {
                    ensureEditor();
                    if (view) view.style.display = 'none';
                    editor.style.display = '';
                    if (editorBox) editorBox.style.display = '';
                    try {
                        const focusEl = editorBox?.querySelector('.trumbowyg-editor') || editor;
                        focusEl?.focus();
                    } catch (_) {}
                    lastActiveEditor = window.$ ? $(editor) : null;
                    lastActiveTextarea = editor;
                    editBtn.style.display = 'none';
                    if (saveBtn) saveBtn.style.display = '';
                    if (cancelBtn) cancelBtn.style.display = '';
                };
                const toViewMode = () => {
                    if (view) view.style.display = '';
                    editor.style.display = 'none';
                    if (!editorBox) editorBox = section.querySelector('.trumbowyg-box');
                    if (editorBox) editorBox.style.display = 'none';
                    editBtn.style.display = '';
                    if (saveBtn) saveBtn.style.display = 'none';
                    if (cancelBtn) cancelBtn.style.display = 'none';
                };

                editBtn.addEventListener('click', () => {
                    toEditMode();
                });
                cancelBtn?.addEventListener('click', () => {
                    toViewMode();
                });

                saveBtn?.addEventListener('click', async () => {
                    let html = editor.value || editor.innerHTML;
                    try {
                        if (window.$ && $(editor).trumbowyg) {
                            html = $(editor).trumbowyg('html');
                        }
                    } catch (e) {}

                    const evidenceId = section.getAttribute('data-evidence-id');
                    const updateUrl = section.getAttribute('data-update-url');

                    try {
                        // disable buttons while saving
                        saveBtn.disabled = true; editBtn.disabled = true; if (cancelBtn) cancelBtn.disabled = true;

                        if (evidenceId && updateUrl) {
                            // Use actual PUT (not _method) and mark X-Requested-With for JSON responses
                            const resp = await fetch(updateUrl, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ detail: html })
                            });
                            const text = await resp.text();
                            let data;
                            try { data = JSON.parse(text); } catch (_) { data = null; }
                            if (resp.ok && (data?.success !== false)) {
                                if (view) view.innerHTML = html || '';
                                toViewMode();
                                window.showToast?.('success', 'บันทึกสำเร็จ');
                            } else {
                                window.showToast?.('error', (data && data.message) || 'บันทึกไม่สำเร็จ');
                            }
                        } else {
                            // Create new detail via store; then reload to reflect new record id
                            const resp = await fetch(storeUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ criteria_id: Number(criteriaId), detail: html })
                            });
                            const text = await resp.text();
                            let data;
                            try { data = JSON.parse(text); } catch (_) { data = null; }
                            if (resp.ok && (data?.success !== false)) {
                                window.showToast?.('success', 'บันทึกสำเร็จ');
                                setTimeout(() => window.location.reload(), 800);
                            } else {
                                window.showToast?.('error', (data && data.message) || 'บันทึกไม่สำเร็จ');
                            }
                        }
                    } catch (err) {
                        window.showToast?.('error', 'เกิดข้อผิดพลาดในการบันทึก');
                    } finally {
                        saveBtn.disabled = false; editBtn.disabled = false; if (cancelBtn) cancelBtn.disabled = false;
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('paste', function(e) {
            const tableHtml = (typeof extractTableHtmlFromClipboard === 'function')
                ? extractTableHtmlFromClipboard(e)
                : (window.extractTableHtmlFromClipboard ? window.extractTableHtmlFromClipboard(e) : '');
            if (tableHtml) {
                e.preventDefault();
                e.stopImmediatePropagation();
                const target = (() => {
                    if (lastActiveEditor && lastActiveEditor.length) return { type: 'trumbowyg', $el: lastActiveEditor };
                    if (lastActiveTextarea) return { type: 'textarea', el: lastActiveTextarea };
                    const boxes = Array.from(document.querySelectorAll('.criteria-detail .trumbowyg-box'));
                    const visibleBox = boxes.find(b => b.offsetParent !== null);
                    if (visibleBox && window.$) {
                        const $ta = $(visibleBox).prev('textarea');
                        if ($ta.length) return { type: 'trumbowyg', $el: $ta };
                    }
                    const textareas = Array.from(document.querySelectorAll('.criteria-detail-editor'));
                    const visibleTextarea = textareas.find(t => t.offsetParent !== null);
                    if (visibleTextarea) return { type: 'textarea', el: visibleTextarea };
                    return null;
                })();
                if (target) {
                    if (target.type === 'trumbowyg') {
                        try {
                            target.$el.trumbowyg('execCmd', { cmd: 'insertHTML', param: tableHtml });
                        } catch (_) {}
                    } else {
                        insertHtmlIntoTextarea(target.el || target.$el?.[0], tableHtml);
                    }
                }
                return;
            }
            const items = e.clipboardData?.items || [];
            let imageItem = null;
            for (const item of items) {
                if (item.type && item.type.indexOf('image') === 0) {
                    imageItem = item;
                    break;
                }
            }
            if (!imageItem) return;

            const resolveTarget = () => {
                if (lastActiveEditor && lastActiveEditor.length) return { type: 'trumbowyg', $el: lastActiveEditor };
                if (lastActiveTextarea) return { type: 'textarea', el: lastActiveTextarea };

                const active = document.activeElement;
                if (active) {
                    const editorDiv = active.closest?.('.trumbowyg-editor');
                    if (editorDiv && window.$) {
                        const $ta = $(editorDiv).closest('.trumbowyg-box').prev('textarea');
                        if ($ta.length) return { type: 'trumbowyg', $el: $ta };
                    }
                    const textAreaEl = active.closest?.('.criteria-detail-editor');
                    if (textAreaEl) return { type: 'textarea', el: textAreaEl };
                }

                const boxes = Array.from(document.querySelectorAll('.criteria-detail .trumbowyg-box'));
                const visibleBox = boxes.find(b => b.offsetParent !== null);
                if (visibleBox && window.$) {
                    const $ta = $(visibleBox).prev('textarea');
                    if ($ta.length) return { type: 'trumbowyg', $el: $ta };
                }

                const textareas = Array.from(document.querySelectorAll('.criteria-detail-editor'));
                const visibleTextarea = textareas.find(t => t.offsetParent !== null);
                if (visibleTextarea) return { type: 'textarea', el: visibleTextarea };

                return null;
            };

            const target = resolveTarget();
            if (!target) return;

            const file = imageItem.getAsFile();
            if (!file) return;

            e.preventDefault();
            e.stopImmediatePropagation();

            const reader = new FileReader();
            reader.onload = function(ev) {
                if (target.type === 'trumbowyg') {
                    try {
                        target.$el.trumbowyg('execCmd', {
                            cmd: 'insertHTML',
                            param: `<img src="${ev.target.result}">`
                        });
                        return;
                    } catch (_) {}
                }
                insertImageIntoTextarea(target.el || target.$el?.[0], ev.target.result);
            };
            reader.readAsDataURL(file);
        }, true);
    </script>
    <script>
        document.addEventListener('dragover', function(e) {
            const hasFiles = e.dataTransfer && Array.from(e.dataTransfer.items || []).some(i => i.kind === 'file');
            if (hasFiles) e.preventDefault();
        }, true);
        document.addEventListener('drop', function(e) {
            const files = Array.from(e.dataTransfer?.files || []);
            const imageFile = files.find(f => f.type && f.type.indexOf('image') === 0);
            if (!imageFile) return;
            e.preventDefault();
            e.stopImmediatePropagation();

            const target = (() => {
                if (lastActiveEditor && lastActiveEditor.length) return { type: 'trumbowyg', $el: lastActiveEditor };
                if (lastActiveTextarea) return { type: 'textarea', el: lastActiveTextarea };
                const boxes = Array.from(document.querySelectorAll('.criteria-detail .trumbowyg-box'));
                const visibleBox = boxes.find(b => b.offsetParent !== null);
                if (visibleBox && window.$) {
                    const $ta = $(visibleBox).prev('textarea');
                    if ($ta.length) return { type: 'trumbowyg', $el: $ta };
                }
                const textareas = Array.from(document.querySelectorAll('.criteria-detail-editor'));
                const visibleTextarea = textareas.find(t => t.offsetParent !== null);
                if (visibleTextarea) return { type: 'textarea', el: visibleTextarea };
                return null;
            })();

            if (!target) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                if (target.type === 'trumbowyg') {
                    try {
                        target.$el.trumbowyg('execCmd', {
                            cmd: 'insertHTML',
                            param: `<img src="${ev.target.result}">`
                        });
                        return;
                    } catch (_) {}
                }
                insertImageIntoTextarea(target.el || target.$el?.[0], ev.target.result);
            };
            reader.readAsDataURL(imageFile);
        }, true);
    </script>
    <script>
        function getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta && meta.content ? meta.content : '';
        }

        function startEditEvidenceName(id) {
            const linkSpan = document.getElementById('evidence-link-' + id);
            const editSpan = document.getElementById('evidence-edit-' + id);
            if (linkSpan && editSpan) {
                // Hide link, show input row
                linkSpan.style.display = 'none';
                editSpan.style.display = 'flex';
                const input = document.getElementById('evidence-input-' + id);
                if (input) {
                    input.focus();
                    input.select();
                }
            }
        }

        function cancelEditEvidenceName(id) {
            const linkSpan = document.getElementById('evidence-link-' + id);
            const editSpan = document.getElementById('evidence-edit-' + id);
            if (linkSpan && editSpan) {
                editSpan.style.display = 'none';
                linkSpan.style.display = '';
            }
        }

        async function saveEvidenceName(id) {
            const editSpan = document.getElementById('evidence-edit-' + id);
            const input = document.getElementById('evidence-input-' + id);
            const linkSpan = document.getElementById('evidence-link-' + id);
            const textSpan = document.getElementById('evidence-name-text-' + id);
            if (!editSpan || !input || !linkSpan || !textSpan) return;

            const url = editSpan.dataset.updateUrl;
            const name = input.value.trim();
            if (!name) {
                alert('กรุณากรอกชื่อไฟล์');
                input.focus();
                return;
            }

            try {
                const res = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    body: JSON.stringify({
                        name
                    })
                });

                if (!res.ok) {
                    let msg = 'บันทึกไม่สำเร็จ';
                    try {
                        const data = await res.json();
                        if (data && data.message) msg = data.message;
                    } catch (_) {}
                    alert(msg);
                    return;
                }

                textSpan.textContent = name;
                editSpan.style.display = 'none';
                linkSpan.style.display = '';
            } catch (e) {
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            }
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
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .criteria-status:hover {
            border-color: #cbd5e1;
            color: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        .criteria-status:focus {
            outline: none;
            border-color: #3b82f6;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        /* สไตล์สำหรับ options ภายใน dropdown */
        .criteria-status option {
            padding: 8px 12px;
            font-weight: 500;
            font-size: 12px;
        }

        /* สีตามสถานะของแต่ละ option */
        .criteria-status option[value="0"] {
            background-color: #fef3c7;
            color: #92400e;
        }

        .criteria-status option[value="1"] {
            background-color: #d1fae5;
            color: #065f46;
        }

        .criteria-status option[value="2"] {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* สไตล์เมื่อ select ถูกเลือกตามค่า */
        .criteria-status.status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .criteria-status.status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .criteria-status.status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
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

        .btn-delete {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #dc2626;
            transition: color 0.2s, transform 0.1s;
        }

        .btn-delete:hover {
            color: #b91c1c;
            transform: scale(1.1);
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

        .variable-input {
            border: 1px solid var(--color-gray-200);
            border-radius: 6px;
            padding: 6px 10px;
            width: 300px;
            text-align: center;
            font-size: 14px;
            background: var(--color-white);
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

        .trumbowyg-editor ol,
        .trumbowyg-editor ul {
            list-style-position: inside;
            padding-left: 0;
        }

        .trumbowyg-editor ol,
        .trumbowyg-editor ul {
            list-style-position: inside;
            padding-left: 0;
        }

        /* ให้กล่อง Trumbowyg กลมกลืนกับธีมเดิม */
        .trumbowyg-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .trumbowyg-editor,
        .trumbowyg-textarea {
            font-size: 16px;
            min-height: 160px;
        }

        .trumbowyg-editor img,
        .criteria-detail-view img {
            max-width: none;
            height: auto;
        }

        .trumbowyg-editor table,
        .criteria-detail-view table {
            min-width: 100%;
            width: max-content;
            table-layout: auto;
        }

        .trumbowyg-editor,
        .criteria-detail-view {
            overflow-x: auto;
        }

        .trumbowyg-editor th,
        .trumbowyg-editor td,
        .criteria-detail-view th,
        .criteria-detail-view td {
            word-break: break-word;
        }

        .trumbowyg-box.trumbowyg-editor-visible .trumbowyg-editor:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
            border-color: #3b82f6;
        }

        .percentage-text {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
        }

        .evidence-containers {
            width: 85%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            margin: 40px auto;
            background: var(--color-white);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .header-containers {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            padding: 10px 14px;
            font-weight: 700;
            background: linear-gradient(90deg, #a9c6ff 0%, var(--color-white)3d4 100%);
            color: #222;
        }

        /* .evidence-form {
                padding: 20px
            } */

        /* Upload */
        .upload-section {
            margin-bottom: 30px;
        }

        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            background: #f9fafb;
            cursor: pointer;
            transition: .3s;
            margin-bottom: 20px;
        }

        .upload-area:hover,
        .upload-area.drag-over {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .upload-icon {
            margin-bottom: 16px;
        }

        .upload-text {
            color: #6b7280;
            margin: 0;
            font-size: 16px;
        }

        .files-list {
            max-height: 200px;
            overflow-y: auto;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 12px;
            background: #f3f4f6;
            border-radius: 6px;
            margin-bottom: 8px;
            gap: 12px;
        }

        .file-name {
            flex: 1;
            font-size: 14px;
            color: #374151;
            word-break: break-all;
        }

        .file-size {
            font-size: 12px;
            color: #6b7280;
            flex-shrink: 0;
        }

        .section-divider {
            position: relative;
            text-align: center;
            color: #6b7280;
            margin: 20px 0;
            font-size: 14px;
            pointer-events: none;
        }

        .section-divider:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
            z-index: 1;
        }

        .section-divider:after {
            content: 'หรือ';
            background: var(--color-white);
            padding: 0 15px;
            position: relative;
            z-index: 2;
        }

        .form-group {
            margin-bottom: 16px;
            display: flex;
            gap: 8px;
        }

        .form-input {
            flex: 1;
            padding: 12px 16px;
            border: 1.5px dashed #d1d5db;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color .3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
        }

        .form-input.locked {
            background: #f3f4f6;
            color: #6b7280;
            pointer-events: none;
        }

        /* Details / Buttons */
        .details-section {
            margin-bottom: 30px;
        }

        .section-title {
            color: #374151;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .editor-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            flex-wrap: wrap;
        }

        .font-select,
        .size-select {
            padding: 4px 8px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: var(--color-white);
            font-size: 14px;
        }

        .toolbar-btn {
            padding: 6px 8px;
            background: none;
            border: 1px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            transition: .2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toolbar-btn:hover {
            background: #e5e7eb;
        }

        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
            font-size: 16px;
            font-family: Arial, sans-serif;
            resize: vertical;
            min-height: 120px;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
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
            .evidence-edit {
                flex-direction: column;
                align-items: center;
            }

            .evidence-edit input {
                width: 100%;
            }

            .evidence-edit .button-group {
                width: 100%;
                align-items: center;
                justify-content: center;
            }
        }

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

            .variable-input {
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

            .variable-input {
                width: 100%;
            }

            /* .evidence-form {
                    padding: 20px;
                } */

            .action-buttons {
                flex-direction: column;
            }

            .form-group {
                flex-direction: column;
            }

            .add-url-btn,
            .remove-url-btn {
                align-self: flex-start;
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

            .variable-input {
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

            .variable-input {
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

            .variable-input {
                width: 300px;
            }

            .action-bts {
                gap: 12px;
            }
        }
    </style>
@endpush
