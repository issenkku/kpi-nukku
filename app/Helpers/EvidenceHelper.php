<?php

if (!function_exists('evidence_preview_url')) {
    /**
     * Build a preview URL for an evidence item.
     * - URL type -> return the first URL
     * - Office docs -> use server preview route (handles conversion to PDF)
     * - Others (pdf/images/etc.) -> return public storage URL
     */
    function evidence_preview_url($evidence): ?string
    {
        if (!$evidence) {
            return null;
        }

        $ext = strtolower($evidence->type ?? pathinfo($evidence->name ?? '', PATHINFO_EXTENSION));

        // URL evidence
        if ($ext === 'url') {
            return $evidence->path['urls'][0] ?? null;
        }

        // File-based evidence (expects path.files[0].path)
        $path = $evidence->path['files'][0]['path'] ?? null;
        if (!$path) {
            return null;
        }

        // Build public URL for fallback viewer integrations
        $fileUrl = asset('storage/' . ltrim($path, '/'));

        // Office documents -> choose viewer by .env (or stream via server)
        if (in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'], true)) {
            $mode = strtolower((string) env('EVIDENCE_OFFICE_VIEWER', 'server'));
            switch ($mode) {
                case 'office_online':
                case 'office':
                case 'msoffice':
                case 'online':
                    return 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($fileUrl);
                case 'google':
                case 'gdocs':
                    return 'https://docs.google.com/gview?embedded=1&url=' . urlencode($fileUrl);
                case 'auto':
                    // Prefer server preview; clients without LibreOffice will fallback in controller
                    return route('evidences.preview', $evidence->id);
                case 'server':
                default:
                    return route('evidences.preview', $evidence->id);
            }
        }

        // Default: stream through controller to avoid broken symlink issues
        // This does not rely on public/storage symlink and works on Windows.
        return route('evidences.preview', $evidence->id);
    }
}
