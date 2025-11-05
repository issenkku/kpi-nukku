<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>PDF Thai Test</title>
    <style>
        @php
            $sarReg = str_replace('\\\\', '/', storage_path('fonts/Sarabun-Regular.ttf'));
            $sarBold = str_replace('\\\\', '/', storage_path('fonts/Sarabun-Bold.ttf'));
        @endphp
        @font-face {
            font-family: 'SarabunLocal';
            font-style: normal;
            font-weight: 400;
            src: url('{{ $sarReg }}') format('truetype');
        }
        @font-face {
            font-family: 'SarabunLocal';
            font-style: normal;
            font-weight: 700;
            src: url('{{ $sarBold }}') format('truetype');
        }

        body { font-family: 'SarabunLocal', sans-serif; font-size: 16px; }
        h1 { font-weight: 700; margin-bottom: 8px; }
    </style>
</head>
<body>
    <h1>ทดสอบภาษาไทย</h1>
    <p>ก ข ฃ ค ฅ ฆ ง จ ฉ ช ซ ฌ ญ ฎ ฏ ฐ ฑ ฒ ณ ด ต ถ ท ธ น บ ป ผ ฝ พ ฟ ภ ม ย ร ล ว ศ ษ ส ห ฬ อ ฮ</p>
    <p>ตัวเลขไทย: ๑ ๒ ๓ ๔ ๕ ๖ ๗ ๘ ๙ ๐</p>
</body>
</html>
