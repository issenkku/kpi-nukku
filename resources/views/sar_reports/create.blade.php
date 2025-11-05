@extends('layouts.app')

@section('content')
    <div class="sar-container">
        <div class="sar-containers">
            <div class="header-contatainers">
                สร้างรายงาน SAR
            </div>
            <div class="max-w-6xl mx-auto space-y-6 px-6 m-6 pb-6">
                {{-- <h2 class="text-2xl font-bold mb-1">สร้างรายงาน SAR</h2> --}}
                @if (request('year'))
                    <div class="text-gray-600 mb-6">ปีการประเมิน: <span class="font-semibold">{{ request('year') }}</span>
                    </div>
                @endif


                <form method="POST" action="{{ route('sar_reports.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="year" value="{{ request('year') }}">
                    {{-- ชื่อเรื่อง --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <label for="title" class="block text-lg font-semibold mb-2">ชื่อเรื่อง (ถ้ามี)</label>
                        <input type="text" name="title" id="title" class="w-full border rounded px-3 py-2"
                            value="{{ old('title') }}" placeholder="เช่น รายงานการประเมินตนเอง ประจำปี 2566">
                    </div>
                    {{-- ส่วนที่ 1 --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4">ส่วนที่ 1: ข้อมูลทั่วไปคณะพยาบาลศาสตร์</h3>
                        <textarea name="section1" id="section1" class="trumbowyg-textarea w-full" aria-label="ส่วนที่ 1">{{ old('section1') }}</textarea>
                    </div>

                    {{-- ส่วนที่ 2 --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4">ส่วนที่ 2: ข้อมูลด้านคุณภาพ</h3>
                        <textarea name="section2" id="section2" class="trumbowyg-textarea w-full" aria-label="ส่วนที่ 2">{{ old('section2') }}</textarea>
                    </div>

                    {{-- ส่วนที่ 3 --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4">ส่วนที่ 3: การประเมินตนเองตามตัวบ่งชี้</h3>

                        @foreach ($standards as $stdName => $indsByStd)
                            <div class="mt-6">
                                <h4 class="text-md font-bold text-blue-700 mb-2">มาตรฐาน: {{ $stdName }}</h4>

                                @foreach ($indsByStd->groupBy('category.name') as $catName => $inds)
                                    <h5 class="text-md font-semibold text-gray-700 mt-4 mb-2">ด้าน: {{ $catName }}</h5>

                                    @foreach ($inds as $ind)
                                        @php
                                            if (request('year') && (string) $ind->year !== (string) request('year')) {
                                                continue;
                                            }
                                        @endphp
                                        <div class="bg-gray-50 p-3 rounded-md border mb-4">
                                            <div class="font-semibold text-gray-800 mb-2">[{{ $ind->code }}]
                                                {{ $ind->name }}
                                            </div>

                                            {{-- ตารางเกณฑ์ --}}
                                            <div class="overflow-x-auto">
                                                <table class="w-full border-collapse text-sm">
                                                    <thead>
                                                        <tr class="bg-gray-200 text-center text-gray-700">
                                                            <th class="border px-2 py-1 w-12">ข้อ</th>
                                                            <th class="border px-2 py-1">เกณฑ์มาตรฐาน</th>
                                                            <th class="border px-2 py-1">ผลการดำเนินงาน</th>
                                                            <th class="border px-2 py-1">รายงานผลการดำเนินงาน</th>
                                                            <th class="border px-2 py-1">เอกสารหลักฐาน</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y">
                                                        @forelse($ind->criterias as $i => $cri)
                                                            <tr class="hover:bg-gray-100">
                                                                <td class="border text-center">{{ $i + 1 }}</td>
                                                                <td class="border px-2">{{ $cri->name }}</td>
                                                                <td class="border text-center">
                                                                    @if ($cri->status)
                                                                        <span class="text-green-600">✓</span>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                @php
                                                                    $__detailEv = $cri->evidences
                                                                        ->sortByDesc(function($e){ return $e->created_at; })
                                                                        ->first(function($e){ return filled($e->detail); });
                                                                    $__initialReport = $__detailEv->detail ?? '';
                                                                @endphp
                                                                <td class="border px-2 text-center cursor-pointer hover:bg-blue-50"
                                                                    x-data="{ open: false, text: @js($__initialReport), saving: false, saved: false, showPreview: false }" @click="open = true"
                                                                    tabindex="0" role="button"
                                                                    @keydown.enter.prevent="open = true"
                                                                    @keydown.space.prevent="open = true"
                                                                    x-init="// init Trumbowyg เมื่อ modal เปิด
                                                                    $watch('open', value => {
                                                                        if (value) {
                                                                            setTimeout(() => {
                                                                                if ($('#editor-{{ $cri->id }}').data('trumbowyg')) $('#editor-{{ $cri->id }}').trumbowyg('destroy');
                                                                                $('#editor-{{ $cri->id }}').trumbowyg({
                                                                                    svgPath: 'https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/icons.svg',
                                                                                    btns: [
                                                                                        ['viewHTML'],
                                                                                        ['undo', 'redo'],
                                                                                        ['formatting'],
                                                                                        ['fontsize', 'fontfamily'],
                                                                                        ['foreColor', 'backColor'],
                                                                                        ['strong', 'em', 'underline', 'del'],
                                                                                        ['unorderedList', 'orderedList'],
                                                                                        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                                                                                        ['horizontalRule'],
                                                                                        ['removeformat'],
                                                                                        ['fullscreen']
                                                                                    ],
                                                                                    plugins: {
                                                                                        fontsize: {
                                                                                            sizeList: ['12px', '14px', '16px', '18px', '20px', '24px', '28px', '32px']
                                                                                        }
                                                                                    },
                                                                                    autogrow: true,
                                                                                }).on('tbwchange', function() { text = $(this).trumbowyg('html'); });
                                                                            }, 200);
                                                                        }
                                                                    })">

                                                                    <!-- สถานะบันทึก -->
                                                                    <div class="mt-1 text-xs" x-show="saved">
                                                                        <span class="text-green-600 font-semibold">✓
                                                                            บันทึกแล้ว</span>
                                                                    </div>

                                                                    <!-- ข้อความแนะนำเมื่อยังไม่มีข้อมูล -->
                                                                    <template
                                                                        x-if="!(text && text.replace(/<[^>]*>/g,'').trim().length)">
                                                                        <div
                                                                            class="mt-1 text-xs text-gray-500 italic select-none">
                                                                            คลิกเพื่อกรอก
                                                                        </div>
                                                                    </template>

                                                                    <!-- ตัวอย่างหากมีข้อมูล -->
                                                                    <template
                                                                        x-if="text && text.replace(/<[^>]*>/g,'').trim().length">
                                                                        <div class="mt-2 text-left">
                                                                            <div class="rte-preview border rounded bg-gray-50 p-2 max-h-28 overflow-auto"
                                                                                x-html="text"></div>
                                                                            <div class="text-[10px] text-gray-500 mt-1">
                                                                                ตัวอย่าง
                                                                            </div>
                                                                        </div>
                                                                    </template>

                                                                    <!-- Modal -->
                                                                    <!-- Modal -->
                                                                    <div x-show="open"
                                                                        class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
                                                                        x-cloak @click.self="open = false">
                                                                        <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6"
                                                                            @click.stop>
                                                                            <!-- ✅ กันไม่ให้คลิกด้านในส่งต่อไปข้างนอก -->
                                                                            <h3 class="text-lg font-semibold mb-4">
                                                                                กรอกรายงานผลการดำเนินงาน
                                                                            </h3>

                                                                            <!-- Rich Text Editor -->
                                                                            <textarea id="editor-{{ $cri->id }}" x-ref="editor">{!! $__initialReport !!}</textarea>

                                                                            <div class="mt-4 flex justify-end space-x-2">
                                                                                <button type="button"
                                                                                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                                                                                    @click.stop="open = false">
                                                                                    <!-- ✅ .stop -->
                                                                                    ยกเลิก
                                                                                </button>
                                                                                <button type="button"
                                                                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                                                                    @click.stop="
                                                                                     saving = true;
                                                                                     fetch('{{ route('sar_reports.criterias.updateReport', $cri->id) }}', {
                                                                                         method: 'POST',
                                                                                         headers: {
                                                                                             'Content-Type': 'application/json',
                                                                                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                                             'Accept': 'application/json'
                                                                                         },
                                                                                         body: JSON.stringify({ report: text })
                                                                                     })
                                                                                     .then(res => res.json())
                                                                                     .then(data => {
                                                                                         saving = false;
                                                                                         saved = true;
                                                                                         open = false;
                                                                                     })
                                                                                     .catch(() => { saving = false; });
                                                                                 ">
                                                                                    <span x-show="!saving">บันทึก</span>
                                                                                    <span
                                                                                        x-show="saving">กำลังบันทึก...</span>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </td>
                                                                <td class="border px-2">
                                                                    @if ($cri->evidences->isNotEmpty())
                                                                        <ul
                                                                            class="list-disc list-inside text-blue-600 text-sm">
                                                                            @foreach ($cri->evidences as $ev)
                                                                                <li>
                                                                                    <a href="{{ route('evidences.download', $ev->id) }}"
                                                                                        class="hover:underline">
                                                                                        {{ $ev->name }}
                                                                                    </a>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                        <span class="text-gray-400">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="border text-center text-gray-500">
                                                                    ไม่มีข้อมูล</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- เกณฑ์การให้คะแนน --}}
                                            <div class="mt-4 border rounded-md overflow-hidden">
                                                <table class="w-full text-sm">
                                                    <thead>
                                                        <tr class="bg-gray-100 text-center">
                                                            <th class="border px-2 py-1">เกณฑ์การให้คะแนน</th>
                                                            <th class="border px-2 py-1">คะแนน</th>
                                                            <th class="border px-2 py-1 w-32">การประเมินตนเอง</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $lines = [];
                                                            if (!empty($ind->comment)) {
                                                                $plain = preg_replace(
                                                                    '/<\/(p|div|li|br)>/i',
                                                                    "\n",
                                                                    $ind->comment,
                                                                );
                                                                $plain = strip_tags($plain);
                                                                $plain = html_entity_decode(
                                                                    $plain,
                                                                    ENT_QUOTES,
                                                                    'UTF-8',
                                                                );
                                                                $lines = preg_split('/\r\n|\r|\n/', $plain);
                                                                $lines = array_filter(array_map('trim', $lines));
                                                            }
                                                            $score = $ind->self_score ?? ($ind->score_acc ?? null);
                                                        @endphp

                                                        @forelse ($lines as $line)
                                                            @php
                                                                $scoreFromLine = null;
                                                                if (
                                                                    preg_match(
                                                                        '/\(\s*(?:([0-9]+(?:\.[0-9]+)?)\s*คะแนน|คะแนน\s*([0-9]+(?:\.[0-9]+)?)|([0-9]+(?:\.[0-9]+)?))\s*\)/u',
                                                                        $line,
                                                                        $mm,
                                                                    )
                                                                ) {
                                                                    $scoreFromLine =
                                                                        (float) (array_values(
                                                                            array_filter([
                                                                                $mm[1] ?? null,
                                                                                $mm[2] ?? null,
                                                                                $mm[3] ?? null,
                                                                            ]),
                                                                        )[0] ?? null);
                                                                }

                                                                $match =
                                                                    $score !== null &&
                                                                    $scoreFromLine !== null &&
                                                                    abs($scoreFromLine - (float) $score) < 0.001;
                                                            @endphp
                                                            <tr class="hover:bg-gray-50">
                                                                <td class="border px-2 text-left">{{ $line }}</td>
                                                                <td class="border text-center">
                                                                    @if ($scoreFromLine !== null)
                                                                        <span
                                                                            class="{{ $match ? 'font-bold text-green-600' : '' }}">
                                                                            {{ $scoreFromLine }} คะแนน
                                                                        </span>
                                                                    @else
                                                                        ........... คะแนน
                                                                    @endif
                                                                </td>
                                                                <td class="border text-center">
                                                                    @if ($match)
                                                                        <span class="text-green-600 font-bold">✓</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td class="border px-2">............................</td>
                                                                <td class="border text-center">........... คะแนน</td>
                                                                <td class="border"></td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    {{-- ส่วนที่ 4 --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4">ส่วนที่ 4:
                            สรุปผลการประเมินตนเองตามเกณฑ์ของสภาการพยาบาล</h3>
                        <textarea name="section4" id="section4" class="trumbowyg-textarea w-full" aria-label="ส่วนที่ 4">{{ old('section4') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('sar_reports.index') }}"
                            class="bg-white text-blue-600 border border-blue-600 px-6 py-2 rounded-md shadow inline-flex items-center hover:bg-blue-50">
                            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> กลับ
                        </a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow inline-flex items-center">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i> บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('styles')
    <!-- Trumbowyg core CSS (v2.31.0 to match working pages) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css">
    <!-- Trumbowyg table plugin CSS (needed for grid UI and clicks) -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/table/ui/trumbowyg.table.min.css">
    <!-- Trumbowyg colors plugin CSS -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/colors/ui/trumbowyg.colors.min.css">
    <!-- Webfont (TH Sarabun via Google Fonts) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap">

    <style>
        .sar-container {
            /* max-width: 1500px; */
            margin: 0 auto;
            padding: 20px;

        }

        .sar-containers {
            width: 100%;
            /* max-width: 1500px; */
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            /* overflow hidden clips Trumbowyg dropdowns (e.g., table grid) */ `r`n            overflow: visible; 
            /* ทำให้มุมมนทำงานดีขึ้น */
        }

        .header-contatainers {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px 20px;
            font-weight: 700;
            font-size: 30px;
            background: linear-gradient(90deg, #a9c6ff 0%, #fff3d4 100%);
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            color: #222;
        }

        /* Ensure list markers (bullets/numbers) are visible inside editor */
        .trumbowyg-box .trumbowyg-editor ul {
            list-style-type: disc !important;
            list-style-position: inside !important;
            margin-left: 1.5em;
            padding-left: 0;
        }

        .trumbowyg-box .trumbowyg-editor ol {
            list-style-type: decimal !important;
            list-style-position: inside !important;
            margin-left: 1.5em;
            padding-left: 0;
        }

        .trumbowyg-box .trumbowyg-editor li {
            display: list-item !important;
        }

        /* Preview HTML list markers */
        .rte-preview ul {
            list-style-type: disc !important;
            list-style-position: inside !important;
            margin-left: 1.5em;
            padding-left: 0;
        }

        .rte-preview ol {
            list-style-type: decimal !important;
            list-style-position: inside !important;
            margin-left: 1.5em;
            padding-left: 0;
        }

        .rte-preview li {
            display: list-item !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- Trumbowyg core (v2.31.0) -->
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/trumbowyg.min.js"></script>

    <!-- Plugins -->
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/fontsize/trumbowyg.fontsize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/colors/trumbowyg.colors.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/table/trumbowyg.table.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/plugins/fontfamily/trumbowyg.fontfamily.min.js"></script>

    <!-- Init Editor -->
    <script>
        $(function() {
            $('#section1, #section2, #section4').trumbowyg({
                svgPath: 'https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/icons.svg',
                btns: [
                    ['viewHTML'],
                    ['undo', 'redo'],
                    ['formatting'],
                    ['fontsize', 'fontfamily'],
                    ['foreColor', 'backColor'],
                    ['strong', 'em', 'underline', 'del'],
                    ['table'],
                    ['superscript', 'subscript'],
                    ['link'],
                    ['insertImage'],
                    ['unorderedList', 'orderedList'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['horizontalRule'],
                    ['removeformat'],
                    ['fullscreen']
                ],
                plugins: {
                    fontsize: {
                        sizeList: ['10px', '12px', '14px', '16px', '18px', '20px', '24px', '28px', '32px',
                            '36px'
                        ]
                    },
                    fontfamily: {
                        fontList: [{
                                name: 'TH Sarabun',
                                family: "'Sarabun','TH Sarabun New', sans-serif"
                            },
                            {
                                name: 'Arial',
                                family: 'Arial, Helvetica, sans-serif'
                            },
                            {
                                name: 'Times New Roman',
                                family: '"Times New Roman", Times, serif'
                            },
                            {
                                name: 'Tahoma',
                                family: 'Tahoma, Geneva, sans-serif'
                            },
                            {
                                name: 'Courier New',
                                family: '"Courier New", Courier, monospace'
                            }
                        ]
                    }
                },
                autogrow: true,
                semantic: true,
                resetCss: true
            })
            .on('tbwinit', function(){
                // Ensure Enter adds new paragraph (Shift+Enter = line break)
                const $box = $(this).closest('.trumbowyg-box');
                const $editor = $box.find('.trumbowyg-editor');
                $editor.off('keydown.customEnter').on('keydown.customEnter', function(e){
                    if (e.key === 'Enter') {
                        if (e.shiftKey) {
                            document.execCommand('insertLineBreak');
                        } else {
                            document.execCommand('insertParagraph');
                        }
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endpush
