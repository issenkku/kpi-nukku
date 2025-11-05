@props([
    // Raw HTML to render
    'html' => '',
    // Placeholder to show when empty
    'empty' => '-',
    // Extra classes for the wrapper
    'class' => '',
])

@once
    @push('styles')
        <style>
            /* Read-only styles to match editor output (shared) */
            .rte-content {
                font-size: .9375rem;
                color: #0f172a;
                /* slate-900-ish */
            }

            .rte-content p {
                margin: .5rem 0;
            }

            /* Lists */
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

            /* Tables */
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
                vertical-align: top;
            }

            /* Code / Pre */
            .rte-content pre {
                background: #f8fafc;
                border: 1px solid #e5e7eb;
                border-radius: .5rem;
                padding: .5rem .625rem;
                overflow-x: auto;
                font-size: .875rem;
            }

            /* Images */
            .rte-content img {
                max-width: 100%;
                height: auto;
            }

            /* Headings */
            .rte-content h1,
            .rte-content h2,
            .rte-content h3,
            .rte-content h4,
            .rte-content h5,
            .rte-content h6 {
                font-weight: 700;
                line-height: 1.25;
                margin: .75rem 0 .5rem;
            }

            .rte-content h1 {
                font-size: 1.5rem;
            }

            .rte-content h2 {
                font-size: 1.25rem;
            }

            .rte-content h3 {
                font-size: 1.125rem;
            }

            /* HR / Blockquote */
            .rte-content hr {
                border: 0;
                border-top: 1px solid #e5e7eb;
                margin: .75rem 0;
            }

            .rte-content blockquote {
                border-left: 4px solid #e5e7eb;
                margin: .5rem 0;
                padding: .25rem .75rem;
                color: #475569;
                background: #f8fafc;
                border-radius: .25rem;
            }
        </style>
    @endpush
@endonce

@php
    $isEmpty = trim((string) $html) === '';
    $content = $isEmpty ? '<span class="text-slate-400">' . $empty . '</span>' : $html;
@endphp

<div {!! $attributes->merge(['class' => 'rte-content max-w-none ' . $class])->toHtml() !!}>
    {!! $content !!}
</div>
