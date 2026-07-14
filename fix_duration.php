<?php

$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $original = $content;

    // 1. Fix the @if condition
    $content = preg_replace(
        '/@if\s*\(\s*\$tour->duration_days\s*&&\s*\$tour->duration_nights\s*\)/',
        '@if($tour->duration_days)',
        $content
    );

    // 2. Fix {{ $tour->duration_days }}N{{ $tour->duration_nights }}Đ
    $content = preg_replace(
        '/\{\{\s*\$tour->duration_days\s*\}\}N\{\{\s*\$tour->duration_nights\s*\}\}Đ/',
        '{{ $tour->duration_days }}N{{ $tour->duration_nights > 0 ? $tour->duration_nights . \'Đ\' : \'\' }}',
        $content
    );

    // 3. Fix {{ $tour->duration_days ?? 0 }}N{{ $tour->duration_nights ?? 0 }}Đ
    $content = preg_replace(
        '/\{\{\s*\$tour->duration_days\s*\?\?\s*0\s*\}\}N\{\{\s*\$tour->duration_nights\s*\?\?\s*0\s*\}\}Đ/',
        '{{ $tour->duration_days ?? 0 }}N{{ ($tour->duration_nights ?? 0) > 0 ? ($tour->duration_nights ?? 0) . \'Đ\' : \'\' }}',
        $content
    );

    // 4. Fix {{ $tour->duration_days }} Ngày {{ $tour->duration_nights }} Đêm
    $content = preg_replace(
        '/\{\{\s*\$tour->duration_days\s*\}\}\s*Ngày\s*\{\{\s*\$tour->duration_nights\s*\}\}\s*Đêm/i',
        '{{ $tour->duration_days }} Ngày {{ $tour->duration_nights > 0 ? $tour->duration_nights . \' Đêm\' : \'\' }}',
        $content
    );

    // 5. Fix {{ $tour->duration_days ?? 0 }} {{ __('ngày') }} {{ $tour->duration_nights ?? 0 }} {{ __('đêm') }}
    $content = preg_replace(
        '/\{\{\s*\$tour->duration_days\s*\?\?\s*0\s*\}\}\s*\{\{\s*__\(\'ngày\'\)\s*\}\}\s*\{\{\s*\$tour->duration_nights\s*\?\?\s*0\s*\}\}\s*\{\{\s*__\(\'đêm\'\)\s*\}\}/',
        '{{ $tour->duration_days ?? 0 }} {{ __(\'ngày\') }}{{ ($tour->duration_nights ?? 0) > 0 ? \' \' . ($tour->duration_nights ?? 0) . \' \' . __(\'đêm\') : \'\' }}',
        $content
    );

    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "Updated $path\n";
    }
}
