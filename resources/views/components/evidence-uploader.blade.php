@props(['criteria', 'storeRoute' => null, 'lockedStatuses' => false])

@php
    $cid = data_get($criteria, 'id');
    $criteriaStatus = data_get($criteria, 'status');
    $isLocked = is_array($lockedStatuses) ? in_array($criteriaStatus, $lockedStatuses) : (bool) $lockedStatuses;
@endphp

<div class="criteria-evidence" x-data="eUploader{{ $cid }}()" x-init="init()">
    <!-- Open button -->
    <button type="button" class="btn btn-outline btn-md " :disabled="{{ $isLocked ? 'true' : 'false' }}"
        @click="openModal()" @if ($isLocked) hidden @endif>
        <i class="fa fa-upload"></i> เพิ่มหลักฐาน
    </button>

    <!-- Backdrop + Modal -->
    <div class="eu-modal-backdrop" x-cloak x-show="open" x-transition.opacity
        @keydown.escape.window.prevent.stop="closeModal()">
        <div class="eu-backdrop-click bg-black/10 opacity-50 " @click="closeModal()"></div>

        <section class="eu-modal" role="dialog" aria-modal="true" aria-labelledby="eu-title-{{ $cid }}"
            x-trap.inert.noscroll="open">
            <!-- Header -->
            <header class="eu-modal-header flex items-center justify-between">
                <h2 id="eu-title-{{ $cid }}" class="flex-1 text-center font-semibold">
                    เพิ่มหลักฐานใหม่
                </h2>
                <button type="button"
                    class="btn btn-delete !w-fit !text-gray-500 hover:!text-red-500 btn btn-xs hover:!shadow-none"
                    @click="closeModal()" aria-label="ปิด">
                    <i data-lucide="x"></i>
                </button>
            </header>

            <div
                class="p-1 md:p-2 lg:p-3 overflow-y-auto max-w-auto min-h-[160px] sm:min-h-[200px] max-h-[70vh] sm:max-h-[75vh] md:max-h-[80vh]">
                <form action="{{ $storeRoute ?? route('evidences.store') }}" method="POST"
                    enctype="multipart/form-data" id="evidence-form-{{ $cid }}" class="eu-form"
                    @submit="beforeSubmit">
                    @csrf
                    <input type="hidden" name="criteria_id" value="{{ $cid }}">

                    <!-- Single column content -->
                    <div class="eu-stack">
                        <!-- Upload -->
                        <div class="eu-block">
                            <div class="eu-section-title">รายงานผลการดำเนินงาน</div>
                            <textarea id="detailEditor-{{ $cid }}" name="detail" class="eu-editor" rows="6" aria-label="รายละเอียดหลักฐาน">{!! old('detail', data_get($criteria, 'report') ?? data_get($criteria, 'detail') ?? '') !!}</textarea>
                        </div>
                        <div class="eu-block">
                            <div class="eu-dropzone" :class="{ 'is-dragover': dragging }"
                                @dragenter.prevent="dragging = true" @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false" @drop.prevent="handleDrop($event)"
                                @click="pickFiles()">
                                <div class="eu-dropzone-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <p class="eu-dropzone-text">
                                    วางไฟล์ที่นี่ หรือ <span class="eu-link">คลิกเพื่อเลือกไฟล์</span>
                                </p>
                                <input type="file" id="fileInput-{{ $cid }}" name="files[]" multiple
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="eu-file-input" aria-label="เลือกไฟล์แนบหลักฐาน"
                                    @change="handleFileInput($event)">
                            </div>
                            <div class="eu-files" x-show="files.length">
                                <template x-for="(f, idx) in files" :key="f._id">
                                    <div class="eu-file">
                                        <div class="eu-file-preview" x-show="f._isImage">
                                            <img :src="f._objectURL" :alt="f.name">
                                        </div>
                                        <div class="eu-file-info">
                                            <!-- input สำหรับแก้ชื่อไฟล์ -->
                                            <input type="text" class="eu-input eu-file-rename" :name="`file_names[]`" aria-label="ชื่อไฟล์ที่อัปโหลด"
                                                x-model="f._customName" :placeholder="f.name">

                                            <div class="eu-file-meta pl-2">
                                                ขนาดไฟล์ : <span x-text="humanSize(f.size)"></span>
                                            </div>
                                        </div>
                                        <button type="button" class="eu-icon-btn danger cursor-pointer !w-[34px]"
                                            @click="removeFile(idx)" aria-label="ลบไฟล์">
                                            <i data-lucide="x"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- URLs -->
                        <div class="eu-block">
                            <div class="eu-section-title ">แนบลิงก์หลักฐาน</div>

                            @foreach (collect(old('additional_urls', [])) as $u)
                                @if ($u !== null && $u !== '')
                                    <div class="eu-url-row is-locked">
                                        <input type="text" class="eu-input" value="{{ $u }}" readonly
                                            tabindex="-1">
                                        <button type="button" class="eu-icon-btn" disabled aria-label="ลบ URL">
                                            <i data-lucide="lock"></i>
                                        </button>
                                    </div>
                                @endif
                            @endforeach

                            <template x-for="(row, i) in urlRows" :key="row._id">
                                <div class="eu-url-row">
                                    <input type="text" class="eu-input" :name="`url_names[]`" aria-label="ชื่อ URL"
                                        placeholder="ชื่อหลักฐาน URL" x-model="row.name">
                                    <input type="url" class="eu-input" :name="`additional_urls[]`" aria-label="ที่อยู่ URL"
                                        placeholder="วาง URL เพิ่มเติม" x-model="row.url">
                                    <button type="button" class="eu-icon-btn danger cursor-pointer"
                                        @click="removeUrl(i)" aria-label="ลบ URL">
                                        <i data-lucide="x"></i>
                                    </button>
                                </div>
                            </template>

                            <div class="eu-url-actions">
                                <button type="button"
                                    class="btn outline text-[14px] text-gray-500 !px-2 !py-1 !gap-0.5 hover:bg-gray-100"
                                    @click="addUrl()">
                                    <i data-lucide="plus" class="text-[14px]"></i> เพิ่ม URL </button>
                            </div>
                        </div>

                        <!-- Details -->

                    </div>
            </div>

            <!-- Sticky Actions -->
            <footer class="eu-actions">
                <button type="button" class="btn btn-outline" @click="closeModal()">
                    <i class="fa fa-undo"></i> ยกเลิก
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> บันทึก
                </button>
            </footer>
            </form>
        </section>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/colors/ui/trumbowyg.colors.min.css">
    <style>
        .trumbowyg-box .trumbowyg-editor-box {
            height: fit-content !important;
        }

        .trumbowyg-box {
            min-height: unset !important;
        }

        .trumbowyg-box {
            overflow: hidden !important;
        }
    </style>
    <style>
        [x-cloak] {
            display: none !important
        }

        .eu-icon-btn {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 9px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            transition: background .2s ease, transform .06s ease, color .2s ease, border-color .2s ease;
        }

        .eu-icon-btn:hover {
            background: #f9fafb;
            transform: translateY(-1px)
        }

        .eu-icon-btn.danger {
            color: #b91c1c;
            border-color: #f3d2d2
        }

        .eu-icon-btn[disabled] {
            opacity: .5;
            cursor: not-allowed
        }

        /* Modal */
        .eu-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 100000;
            display: none
        }

        .eu-modal-backdrop[x-show="open"] {
            display: block
        }

        .eu-backdrop-click {
            position: absolute;
            inset: 0;
        }

        .eu-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 61;
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 720px;
            max-height: 92vh;
            overflow: hidden;
            /* padding: .75rem .75rem 0.5rem; */
            box-shadow: 0 20px 55px rgba(0, 0, 0, .18);
        }

        .eu-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .25rem .25rem .6rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .eu-modal-header h2 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #111827;
            margin: 0
        }

        /* Form layout (single column) */
        .eu-form {
            display: flex;
            flex-direction: column;
            gap: 0
        }

        .eu-stack {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: .75rem .25rem
        }

        .eu-block {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: .9rem;
        }

        .eu-section-title {
            color: black;
            margin: 0 0 .6rem;
            font-size: 14px;
            font-weight: 600;

        }

        /* Dropzone */
        .eu-dropzone {
            border: 1.5px dashed #d1d5db;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            background: #fafafa;
            transition: background .2s ease, border-color .2s ease;
        }

        .eu-dropzone:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.12);
        }

        .eu-dropzone.is-dragover {
            background: #f3f4f6;
            border-color: #9ca3af
        }

        .eu-dropzone-icon {
            display: grid;
            place-items: center;
            margin-bottom: .25rem;
            color: #9ca3af
        }

        .eu-dropzone-text {
            color: #374151;
            font-size: 14px;
        }

        .eu-link {
            text-decoration: underline
        }

        .eu-file-input {
            display: none
        }

        /* Files list (stack) */
        .eu-files {
            display: flex;
            flex-direction: column;
            gap: .6rem;
            margin-top: .8rem
        }

        .eu-file {
            display: flex;
            align-items: top;
            /* justify-content: space-between; */
            gap: .7rem;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: .55rem .6rem;
            background: #fff;
        }

        .eu-file-preview {
            width: 46px;
            height: 46px;
            border-radius: 8px;
            overflow: hidden;
            background: #f3f4f6;
            flex: 0 0 auto
        }

        .eu-file-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .eu-file-info {
            flex: 1 1 auto;
            min-width: 0
        }

        .eu-file-name {
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .eu-file-meta {
            font-size: 13px;
            color: #6b7280;
        }

        /* URL rows (single column with inline delete) */
        .eu-url-row {
            display: grid;
            gap: .5rem;
            grid-template-columns: 1fr 1fr auto;
            align-items: center;
            margin-bottom: .5rem;
        }

        .eu-url-row.is-locked input {
            background: #f9fafb;
            color: #6b7280
        }

        .eu-url-actions {
            display: flex;
            justify-content: flex-end;
        }

        /* Inputs */
        .eu-input {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: .5rem .75rem;
            color: #111827;
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease;
            font-size: 14px;
            cursor: pointer;
        }

        .eu-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.12);
        }

        .eu-input:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.12);
        }

        .eu-editor {
            width: 100%;
            height: fit-content;
        }

        /* Footer actions */
        .eu-actions {
            position: sticky;
            bottom: 0;
            background: #fff;
            border-top: 1px solid #f3f4f6;
            display: flex;
            justify-content: flex-end;
            gap: .5rem;
            padding: 10px 20px;
            z-index: 12;
        }

        .eu-modal-header {
            position: sticky;
            top: 0;
            background: #fff;
            border-top: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            gap: .5rem;
            padding: 10px 10px;
            z-index: 12;
        }

        @media (max-width:560px) {
            .eu-url-row {
                grid-template-columns: 1fr;
                gap: .5rem;
                margin-bottom: 1rem;
            }

            .eu-icon-btn {
                width: 100%
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/langs/th.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/colors/trumbowyg.colors.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/fontsize/trumbowyg.fontsize.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/fontfamily/trumbowyg.fontfamily.min.js">
    </script>

    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>

    <!-- Alpine Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        function eUploader{{ $cid }}() {
            return {
                open: false,
                dragging: false,
                files: [],
                urlRows: [],

                // Safe UUID generator that works without secure context (HTTP)
                uuid() {
                    try {
                        if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                            return window.crypto.randomUUID();
                        }
                    } catch (e) {
                        /* noop */
                    }
                    try {
                        if (window.crypto && window.crypto.getRandomValues) {
                            const buf = new Uint8Array(16);
                            window.crypto.getRandomValues(buf);
                            buf[6] = (buf[6] & 0x0f) | 0x40; // version 4
                            buf[8] = (buf[8] & 0x3f) | 0x80; // variant 10
                            const hex = Array.from(buf, b => b.toString(16).padStart(2, '0')).join('');
                            return `${hex.substring(0, 8)}-${hex.substring(8, 12)}-${hex.substring(12, 16)}-${hex.substring(16, 20)}-${hex.substring(20)}`;
                        }
                    } catch (e) {
                        /* noop */
                    }
                    // Math.random fallback
                    let d = Date.now();
                    let d2 = (typeof performance !== 'undefined' && performance.now) ? performance.now() * 1000 : 0;
                    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
                        let r = Math.random() * 16;
                        if (d > 0) {
                            r = (d + r) % 16 | 0;
                            d = Math.floor(d / 16);
                        } else {
                            r = (d2 + r) % 16 | 0;
                            d2 = Math.floor(d2 / 16);
                        }
                        return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                    });
                },

                init() {
                    this.$nextTick(() => {
                        $('#detailEditor-{{ $cid }}').trumbowyg({
                            lang: 'th',
                            resetCss: true,
                            removeformatPasted: true,
                            btns: [
                                ['viewHTML'],
                                ['undo', 'redo'],
                                ['formatting'],
                                ['strong', 'em', 'del'],
                                ['fontsize', 'foreColor'],
                                ['link'],
                                ['unorderedList', 'orderedList'],
                                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                                ['horizontalRule'],
                                ['removeformat']
                            ]
                        });
                        // Ensure at least one URL row exists
                        if (!this.urlRows.length) {
                            this.urlRows.push({
                                _id: this.uuid(),
                                name: '',
                                url: ''
                            });
                        }
                        this.refreshIcons();
                    });
                },

                openModal() {
                    this.open = true;
                    document.documentElement.style.overflow = 'hidden';
                    this.$nextTick(() => this.refreshIcons());
                },
                closeModal() {
                    this.open = false;
                    document.documentElement.style.overflow = '';
                },

                refreshIcons() {
                    if (window.lucide) window.lucide.createIcons();
                },

                pickFiles() {
                    this.$root.querySelector('#fileInput-{{ $cid }}').click();
                },
                handleFileInput(e) {
                    const list = Array.from(e.target.files || []);
                    this.addFiles(list);
                },
                handleDrop(e) {
                    this.dragging = false;
                    const list = Array.from(e.dataTransfer.files || []);
                    this.addFiles(list);
                },
                addFiles(list) {
                    const accept = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                    for (const f of list) {
                        const ext = (f.name.split('.').pop() || '').toLowerCase();
                        if (!accept.includes(ext)) continue;

                        f._id = this.uuid();
                        f._isImage = ['jpg', 'jpeg', 'png'].includes(ext);
                        if (f._isImage) f._objectURL = URL.createObjectURL(f);

                        // 🔹 ตั้งค่า default name = original
                        f._customName = f.name;

                        this.files.push(f);
                    }
                    this.syncNativeInput();
                    this.$nextTick(() => this.refreshIcons());
                },

                removeFile(idx) {
                    const f = this.files[idx];
                    if (f && f._objectURL) URL.revokeObjectURL(f._objectURL);
                    this.files.splice(idx, 1);
                    this.syncNativeInput();
                },
                syncNativeInput() {
                    const input = this.$root.querySelector('#fileInput-{{ $cid }}');
                    const dt = new DataTransfer();
                    this.files.forEach(f => dt.items.add(f));
                    input.files = dt.files;
                },
                humanSize(bytes) {
                    const units = ['B', 'KB', 'MB', 'GB'];
                    let i = 0;
                    while (bytes >= 1024 && i < units.length - 1) {
                        bytes /= 1024;
                        i++;
                    }
                    return `${bytes.toFixed(i===0 ? 0 : 1)} ${units[i]}`;
                },

                addUrl() {
                    this.urlRows.push({
                        _id: this.uuid(),
                        name: '',
                        url: ''
                    });
                    this.$nextTick(() => this.refreshIcons());
                },
                removeUrl(i) {
                    this.urlRows.splice(i, 1);
                },

                beforeSubmit(e) {
                    this.urlRows = this.urlRows.filter(r => (r.name?.trim() || r.url?.trim()));
                    this.syncNativeInput();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) window.lucide.createIcons();
        });
    </script>
@endpush
