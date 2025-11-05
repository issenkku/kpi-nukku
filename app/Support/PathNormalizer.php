<?php

namespace App\Support;

class PathNormalizer
{
    public static function normalize(?string $path): ?string
    {
        if ($path === null) return null;

        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        return preg_replace('#^(storage(?:/app)?/public/|public/|storage/)+#i', '', $path);
    }
}
