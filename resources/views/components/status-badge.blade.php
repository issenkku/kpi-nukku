@props([
    // Accepts 0–4 (number or string). Falls back to label/string if not mapped.
    'status' => null,
    // Optional: force a specific label (overrides mapping label).
    'label' => null,
    // Size: sm | md | lg
    'size' => 'sm',
])

@php
    // Map status -> label + colors (shadcn-like)
    $map = [
        0 => ['label' => 'รอดำเนินการ',                          'class' => 'bg-slate-100 text-slate-800 ring-slate-300',     'dot' => 'bg-slate-500'],
        1 => ['label' => 'บันทึกร่าง',                            'class' => 'bg-amber-100 text-amber-800 ring-amber-300',     'dot' => 'bg-amber-600'],
        2 => ['label' => 'บันทึกจริง',                            'class' => 'bg-blue-100 text-blue-800 ring-blue-300',        'dot' => 'bg-blue-600'],
        3 => ['label' => 'ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรฐาน',   'class' => 'bg-emerald-100 text-emerald-800 ring-emerald-300','dot' => 'bg-emerald-600'],
        4 => ['label' => 'ผลการดำเนินงานไม่ครบถ้วนตามเกณฑ์มาตรฐาน', 'class' => 'bg-rose-100 text-rose-800 ring-rose-300',       'dot' => 'bg-rose-600'],
    ];

    $key = is_numeric($status) ? (int) $status : null;
    $conf = $key !== null && array_key_exists($key, $map) ? $map[$key] : null;

    $resolvedLabel = $label
        ?? ($conf['label'] ?? ( ($status !== null && $status !== '') ? (string)$status : '-' ));

    // sizes
    $sizeClass = match($size) {
        'md' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-3.5 py-2 text-sm',
        default => 'px-2.5 py-1 text-xs', // sm
    };

    $badgeBase = 'inline-flex items-center rounded-full font-medium ring-1 ring-inset';
    $toneClass = $conf['class'] ?? 'bg-slate-100 text-slate-700 ring-slate-300';
    $dotClass  = $conf['dot']   ?? 'bg-slate-500';
@endphp

<span {{ $attributes->class("$badgeBase $sizeClass $toneClass") }}>
    <span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>
    {{ $resolvedLabel }}
</span>
