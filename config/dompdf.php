<?php

return [
    'show_warnings' => false,
    'public_path'   => null,
    'convert_entities' => true,

    'font_family' => [
        'SarabunLocal' => [
            'R' => 'Sarabun-Regular.ttf',
            'B' => 'Sarabun-Bold.ttf',
        ],
        'NotoSansThai' => [
            'R' => 'NotoSansThai-Regular.ttf',
            'B' => 'NotoSansThai-Bold.ttf',
        ],
    ],

    'options' => [
        'font_dir'   => storage_path('fonts'),
        'font_cache' => storage_path('fonts'),
        // Use Sarabun as the default to ensure Thai is rendered
        'default_font' => 'SarabunLocal',
        'enable_font_subsetting' => true,
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
    ],
];
