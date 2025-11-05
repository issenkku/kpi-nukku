@props([
    'name',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'preview' => true,
    'height' => 260, // px
])

@php
    $inputId = $name . '_trix';
    $editorId = $name . '_trumbowyg';
@endphp

@once
    @push('styles')
        <style>
            /* ---------- Shell (จากหน้า create) ---------- */
            .trumbowyg-box {
                border-radius: 1rem;
                /* rounded-2xl */
                border: 1px solid #e5e7eb;
                /* slate-200 */
                box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
                /* overflow: hidden; */
                background: #fff;
            }

            /* ---------- Toolbar (จากหน้า create + เสริม focus) ---------- */
            .trumbowyg-box .trumbowyg-button-pane {
                display: flex;
                flex-wrap: wrap;
                gap: .25rem .375rem;
                padding: .375rem .5rem;
                /* background: #fff; */
                border-top-left-radius: 1rem;
                border-top-right-radius: 1rem;

            }

            .trumbowyg-box .trumbowyg-button-pane::before,
            .trumbowyg-box .trumbowyg-button-pane::after {
                content: none;
                display: none;
            }

            .trumbowyg-box .trumbowyg-button-group {
                display: flex;
                gap: .25rem;
            }

            .trumbowyg-box .trumbowyg-button-pane button {
                border-radius: .5rem;
                padding: .25rem;
                transition: background .15s ease;
                outline: none;
            }

            .trumbowyg-box .trumbowyg-button-pane button:hover {
                background: #f1f5f9;
            }

            /* slate-100 */
            .trumbowyg-box .trumbowyg-button-pane button:focus-visible {
                box-shadow: 0 0 0 3px rgba(59, 130, 246, .3);
                background: #eff6ff;
            }

            .trumbowyg-box .trumbowyg-button-pane button:active {
                transform: translateY(.5px);
                background: #e2e8f0;
            }

            .trumbowyg-box .trumbowyg-button-pane button.trumbowyg-active,
            .trumbowyg-box .trumbowyg-button-pane button.trumbowyg-active-btn {
                background: #dbeafe;
            }

            @media (max-width:640px) {
                .trumbowyg-box .trumbowyg-button-pane {
                    padding: .25rem .5rem;
                }

                .trumbowyg-box .trumbowyg-button-pane button {
                    transform: scale(.95);
                }
            }

            /* ---------- Editor area (รวมของเดิม + หน้า create) ---------- */
            .trumbowyg-editor ol,
            .trumbowyg-editor ul {
                list-style-position: inside;
                /* สำคัญ */
                padding-left: 0;
                /* ตัดระยะเว้นซ้ายของลิสต์เดิม */
            }

            .trumbowyg-box .trumbowyg-editor {
                padding: .75rem;
                font-size: .9375rem;
                position: relative;
            }

            /* Lists (เหมือนในหน้า create) */
            .trumbowyg-box .trumbowyg-editor ul {
                list-style: disc;
                padding-left: 1.25rem;
            }

            .trumbowyg-box .trumbowyg-editor ol {
                list-style: decimal;
                padding-left: 1.5rem;
            }

            .trumbowyg-box .trumbowyg-editor li {
                list-style: inherit;
                margin: .25rem 0;
            }

            /* Tables (รวม fix ฟอนต์/ขนาด) */
            .trumbowyg-box .trumbowyg-editor table {
                width: 100%;
                border-collapse: collapse;
                font-size: inherit;
            }

            .trumbowyg-box .trumbowyg-editor th,
            .trumbowyg-box .trumbowyg-editor td {
                border: 1px solid #e5e7eb;
                padding: .5rem .625rem;
                font-size: inherit;
                vertical-align: top;
            }

            /* Dropdowns */
            .trumbowyg-box .trumbowyg-dropdown {
                border: 1px solid #e5e7eb;
                border-radius: .75rem;
                box-shadow: 0 10px 20px rgba(0, 0, 0, .06);
            }

            /* ---------- Preview content (ของคอมโพเนนต์เดิม) ---------- */
            .rte-content {
                font-size: .9375rem;
            }

            .rte-content p {
                margin: .5rem 0;
            }

            .rte-content ul {
                list-style: disc;
                padding-left: 1.25rem;
            }

            .rte-content ol {
                list-style: decimal;
                padding-left: 1.5rem;
            }

            .rte-content li {
                margin: .25rem 0;
            }

            .rte-content table {
                width: 100%;
                border-collapse: collapse;
                font-size: .95rem;
            }

            .rte-content table,
            .rte-content th,
            .rte-content td {
                border: 1px solid #e5e7eb;
            }

            .rte-content th,
            .rte-content td {
                padding: .5rem .625rem;
            }

            /* ---------- Placeholder (สองรูปแบบ: fallback :empty + แบบคลาส) ---------- */
            .trumbowyg-box .trumbowyg-editor[contenteditable="true"][placeholder]:empty::before {
                content: attr(placeholder);
                color: #94a3b8;
                opacity: .9;
            }

            .trumbowyg-box .trumbowyg-editor.rte-empty::before {
                content: attr(placeholder);
                position: absolute;
                left: .75rem;
                top: .75rem;
                color: #94a3b8;
                opacity: .9;
                pointer-events: none;
                user-select: none;
            }

            /* Alpine FOUC */
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush
@endonce

<div x-data="{ html: @js(old($name, $value)), showPreview: false }" class="block">
    @if ($label)
        <label for="{{ $inputId }}" class="text-sm font-medium text-slate-700 mb-1 block">{{ $label }}</label>
    @endif

    <input id="{{ $inputId }}" type="hidden" name="{{ $name }}" x-model="html"
        value="{{ old($name, $value) }}" autocomplete="off">

    <div id="{{ $editorId }}"
        @class([
            'rounded-b-2xl w-full h-full border bg-white shadow-sm overflow-hidden',
            'border-red-500' => $errors->has($name),
            'border-slate-200' => !$errors->has($name),
        ])
        ></div>
    
    <x-input-error :name="$name" />

    @if ($preview)
        <div class="mt-3" x-data="{ placeholder: 'พิมพ์เพื่อดูตัวอย่าง…' }">
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-slate-700">ตัวอย่าง (Preview)</span>
                <label class="text-xs text-slate-600 flex items-center gap-2">
                    <input type="checkbox" x-model="showPreview" 
                        id="{{ $name }}_preview_toggle" 
                        name="{{ $name }}_preview_toggle" 
                        class="rounded border-slate-300" 
                        autocomplete="off">
                    แสดงตัวอย่าง
                </label>
            </div>

            <div x-show="showPreview" x-cloak
                class="rte-content rounded-xl border border-slate-200 bg-slate-50 p-3 min-h-[2.5rem] max-w-none"
                x-html="(html && html.trim().length)
             ? html
             : `<span class='text-slate-400'>${placeholder}</span>`">
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            (function() {
                function initOne(editorId, inputId, initialHtml, placeholder) {
                    var $host = $('#' + editorId);
                    if (!$host.length || $host.data('rteInited')) return; // guard against double init
                    $host.data('rteInited', true);

                    var raf = null,
                        editorEl = null,
                        normalizing = false;

                    function visuallyEmpty(html) {
                        return (html || '')
                            .replace(/<p>(?:\s|&nbsp;|<br\s*\/?>)*<\/p>/gi, '')
                            .replace(/<br\s*\/?>/gi, '')
                            .replace(/&nbsp;/gi, ' ')
                            .replace(/<\/?p[^>]*>/gi, '')
                            .trim() === '';
                    }

                    function normalizeTables() {
                        if (!editorEl) return;
                        $(editorEl).find('table').not('[data-rte-fixed]').each(function() {
                            var $t = $(this);
                            $t.css({
                                width: '100%'
                            });
                            $t.find('th,td').css({
                                'font-size': 'inherit'
                            });
                            $t.attr('data-rte-fixed', '1');
                        });
                    }

                    // Keep the editor truly empty when there's only filler nodes,
                    // AND toggle a class so placeholder shows even if Trumbowyg re-inserts <p><br>.
                    function updatePlaceholderState() {
                        if (!editorEl) return;
                        var html = $host.trumbowyg('html') || '';
                        var isEmptyVisual = visuallyEmpty(html);
                        // make DOM empty so :empty fallback can work
                        if (!normalizing && html !== '' && isEmptyVisual) {
                            normalizing = true;
                            $host.trumbowyg('html', '');
                            normalizing = false;
                        }
                        // class-based placeholder (always works, even when not :empty)
                        editorEl.classList.toggle('rte-empty', isEmptyVisual);
                    }

                    function sync() {
                        var html = $host.trumbowyg('html') || '';
                        var input = document.getElementById(inputId);
                        if (input) {
                            input.value = html;
                            input.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    }

                    function scheduleUpdate() {
                        if (raf) cancelAnimationFrame(raf);
                        raf = requestAnimationFrame(function() {
                            normalizeTables();
                            updatePlaceholderState();
                            sync();
                        });
                    }

                    $host.trumbowyg({
                        autogrow: true,
                        btns: [
                            ['undo', 'redo'],
                            ['strong', 'em', 'del'],
                            ['link'],
                            ['table'],
                            ['unorderedList', 'orderedList'],
                            ['justifyLeft', 'justifyCenter', 'justifyRight'],
                            ['horizontalRule'],
                            ['removeformat'],
                            ['viewHTML'],
                        ],
                        svgPath: 'https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/icons.svg',
                    });

                    $host.on('tbwinit', function() {
                        if ((initialHtml || '').trim().length) {
                            $host.trumbowyg('html', initialHtml);
                        }

                        editorEl = $host.closest('.trumbowyg-box').find('.trumbowyg-editor')[0];
                        if (editorEl) {
                            var ph = placeholder || 'พิมพ์เพื่อเริ่มต้น…';
                            editorEl.setAttribute('placeholder', ph);
                            editorEl.setAttribute('aria-label', ph);

                            $(editorEl).on('input paste blur keyup', scheduleUpdate);
                            $host.on('tbwchange', scheduleUpdate);
                        }

                        // first pass (normalize + placeholder + sync)
                        scheduleUpdate();

                        // ensure final sync on submit
                        var form = $host.closest('form')[0];
                        if (form) form.addEventListener('submit', sync);
                    });
                }

                function ready(fn) {
                    if (document.readyState !== 'loading') fn();
                    else document.addEventListener('DOMContentLoaded', fn);
                }
                ready(function check() {
                    if (window.jQuery && $.fn.trumbowyg) {
                        initOne(@json($editorId), @json($inputId),
                            @json(old($name, $value) ?? ''), @json($placeholder ?? ''));
                        return;
                    }
                    setTimeout(check, 30);
                });
            })();
        </script>
    @endpush
</div>
