<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Criteria;
use App\Models\Department;
use App\Models\Evidence;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class EvidenceController extends Controller
{
    public function index(Request $request)
    {
        // preload ความสัมพันธ์ครบ
        $query = Evidence::with([
            'criteria.indicator.category.standard',
            'criteria.indicator.assignments.collectorUser.department',
            'user'
        ]);

        // 🟢 ถ้า role = user → แสดงเฉพาะของตัวเอง
        if (auth()->user()->hasRole('user')) {
            $query->where('user_id', auth()->id());
        }

        // filter ต่างๆ (optionally)
        if ($request->filled('criteria_id')) {
            $query->where('criteria_id', (int) $request->input('criteria_id'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', (int) $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('indicator_id')) {
            $query->whereHas('criteria.indicator', function ($q) use ($request) {
                $q->where('id', (int) $request->input('indicator_id'));
            });
        }

        // ✅ Dropdown data
        $years = Indicator::whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year');
        $standards = Standard::select('name')->distinct()->orderBy('name')->pluck('name');
        $dimensions = Category::select('name')->distinct()->orderBy('name')->pluck('name');
        $departments = Department::select('name')->distinct()->orderBy('name')->pluck('name');
        $collectors = User::whereHas('assignments')
            ->select('first_name', 'last_name')
            ->get()
            ->pluck('display_name')
            ->unique()
            ->sort()
            ->values();
        $fileTypes = Evidence::whereNotNull('type')->distinct()->orderBy('type')->pluck('type');
        $statusMap = [
            0 => 'รอดำเนินการ',
            1 => 'รอดำเนินการ / บันทึกร่าง',
            2 => 'รอดำเนินการ / บันทึกจริง',
            3 => 'ครบถ้วนตามเกณฑ์',
            4 => 'ยังไม่ครบถ้วนตามเกณฑ์',
        ];
        $statusList = Indicator::pluck('status')->unique()->map(fn($s) => $statusMap[$s] ?? 'ไม่ทราบ');
        // Pagination
        $perPage = (int) $request->input('per_page', 1000);
        $evidences = $query->paginate($perPage)->withQueryString();

        // คำนวณขนาดไฟล์รวม
        $evidences->getCollection()->transform(function ($evidence) {
            $totalSize = 0;
            if (!empty($evidence->path['files'])) {
                foreach ($evidence->path['files'] as $f) {
                    $totalSize += $f['size'] ?? 0;
                }
            }
            $evidence->total_size = $totalSize;
            $evidence->total_size_human = $this->formatFileSize($totalSize);
            return $evidence;
        });

        return view('evidences.app', compact(
            'evidences',
            'years',
            'standards',
            'dimensions',
            'departments',
            'collectors',
            'fileTypes',
            'statusList',
        ));
    }

    public function create(Criteria $criteria)
    {

        $criterias = Criteria::orderBy('name')->get(['id', 'name']);

        if ($criterias->isEmpty()) {
            Log::warning('No criterias found when trying to create evidence.');
            return redirect()
                ->route('dashboard.index')
                ->with('warning', 'ยังไม่มีเกณฑ์ที่ใช้งานอยู่ กรุณาเพิ่ม/เปิดใช้งานเกณฑ์ก่อน');
        }

        // Log::info('Render evidences.create view', [
        //     'criterias_count' => $criterias->count(),
        // ]);

        return view('evidences.create', [
            'criterias'   => $criterias,
            'criteria_id' => $criteria->id, // ✅ ส่งไปด้วย
            'criteria'    => $criteria      // ถ้าอยากใช้ทั้ง object
        ]);
    }



    public function store(Request $request)
    {

        $request->validate([
            'criteria_id'       => 'required|integer|exists:criterias,id',
            'files.*'           => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx',
            'file_names'        => 'nullable|array',
            'file_names.*'      => 'nullable|string|max:255',
            'additional_urls'   => 'nullable|array',
            'additional_urls.*' => 'nullable|url|max:2048',
            'url_names'         => 'nullable|array',
            'url_names.*'       => 'nullable|string|max:255',
            'detail'            => 'nullable|string|max:16777215',
        ]);


        $uploadedFiles = [];
        $savedEvidences = []; // ✅ เก็บ evidences หลายรายการ

        try {
            $urls     = collect($request->input('additional_urls', []));
            $urlNames = collect($request->input('url_names', []));

            $urlEntries = $urls
                ->filter(fn($u) => filled($u))
                ->values()
                ->map(function ($url, $i) use ($urlNames) {
                    return [
                        'url'  => $url,
                        'name' => $urlNames->get($i) ?: 'หลักฐาน URL',
                    ];
                });

            $hasFiles  = $request->hasFile('files');
            $hasUrls   = $urlEntries->isNotEmpty();
            $hasDetail = filled($request->input('detail'));
            $detailAssigned = false; // one detail per criteria

            if (!$hasFiles && !$hasUrls && !$hasDetail) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'กรุณาระบุข้อมูลอย่างน้อย 1 รายการ (ไฟล์, URL หรือรายละเอียด)',
                    ], 422);
                }
                return back()->withInput()->withErrors([
                    'general' => 'กรุณาระบุข้อมูลอย่างน้อย 1 รายการ (ไฟล์, URL หรือรายละเอียด)',
                ]);
            }

            // ✅ chain: criteria -> indicator -> category -> standard
            $criteria  = Criteria::with('indicator.category.standard')->findOrFail($request->criteria_id);
            $indicator = $criteria->indicator;
            $category  = $indicator->category;
            $standard  = $category->standard;

            if (!$indicator || !$category || !$standard) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'กรุณาตรวจสอบว่าตัวบ่งชี้นี้มีการผูกกับหมวดหมู่และมาตรฐานแล้ว',
                    ], 422);
                }
                return back()->withErrors([
                    'general' => 'กรุณาตรวจสอบว่าตัวบ่งชี้นี้มีการผูกกับหมวดหมู่และมาตรฐานแล้ว',
                ]);
            }

            // ใช้ slug กันชื่อไทย/ช่องว่าง
            $standardSegment = $this->safeFolderSegment($standard->name ?? '', 'standard-' . $standard->name);
            $categorySegment = $this->safeFolderSegment($category->name ?? '', 'category-' . $category->name);

            $folder = implode('/', [
                'evidences',
                'year',
                $indicator->year,
                $standardSegment,
                $categorySegment,
                $indicator->code,
            ]);

            // ========== 1) ถ้ามีไฟล์ → loop แล้วบันทึก ==========
            if ($hasFiles) {
                $customNames = $request->input('file_names', []);

                foreach ($request->file('files') as $i => $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension    = strtolower($file->getClientOriginalExtension());

                    // 🔹 ใช้ชื่อจาก input ถ้ามี ไม่งั้น fallback เป็นชื่อไฟล์เดิม
                    $customName   = $customNames[$i] ?? pathinfo($originalName, PATHINFO_FILENAME);
                    $safeName     = Str::slug(pathinfo($customName, PATHINFO_FILENAME), '_');
                    $filename     = $safeName . '_' . uniqid() . '.' . $extension;

                    $path = $file->storeAs($folder, $filename, 'public');

                    $payload = [
                        'files' => [[
                            'original_name' => $originalName,
                            'custom_name'   => $customName,
                            'stored_name'   => $filename,
                            'path'          => $path,
                            'size'          => $file->getSize(),
                            'mime_type'     => $file->getMimeType(),
                            'icon'          => $this->getFileTypeIcon($file->getMimeType()),
                            'size_human'    => $this->formatFileSize($file->getSize()),
                        ]]
                    ];

                    $evidence = new Evidence();
                    $evidence->path        = $payload;
                    // Assign detail only once per criteria (first file or first URL)
                    if ($hasDetail && !$detailAssigned) {
                        $evidence->detail = $request->input('detail');
                        $detailAssigned = true;
                    } else {
                        $evidence->detail = null;
                    }
                    $evidence->status      = false;
                    $evidence->criteria_id = $criteria->id;
                    $evidence->user_id     = Auth::id();
                    $evidence->name        = $customName ?: $originalName; // 🔹 บันทึกชื่อใหม่
                    $evidence->type        = $extension;
                    $evidence->save();

                    $uploadedFiles[] = ['path' => $path];
                }
            }

            // ========== 2) ถ้ามี URL → บันทึก ==========
            if ($hasUrls) {
                foreach ($urlEntries as $entry) {
                    $payload = ['urls' => [$entry['url']]];

                    $evidence = new Evidence();
                    $evidence->path        = $payload;
                    // Assign detail only once per criteria (first URL if not used by files)
                    if ($hasDetail && !$detailAssigned) {
                        $evidence->detail = $request->input('detail');
                        $detailAssigned = true;
                    } else {
                        $evidence->detail = null;
                    }
                    $evidence->status      = true;
                    $evidence->criteria_id = $criteria->id;
                    $evidence->user_id     = Auth::id();
                    $evidence->name        = $entry['name'];
                    $evidence->type        = "url";
                    $evidence->save();

                    // Log::info('Evidence saved (url)', [
                    //     'evidence_id' => $evidence->id,
                    //     'name'        => $evidence->name,
                    //     'url'         => $entry['url'],
                    // ]);
                }
            }

            // ========== 3) ถ้ามีแค่ Detail ==========
            if (!$hasUrls && !$hasFiles && $hasDetail) {
                // เก็บรายงานผลลง criterias.report เท่านั้น (ไม่สร้าง evidence ใหม่)
                $criteria->report = $request->input('detail');
                $criteria->save();
            }

            if ($hasDetail && ($hasFiles || $hasUrls)) {
                // Sync report for quick access when evidences are not loaded
                $criteria->report = $request->input('detail');
                $criteria->save();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'บันทึกหลักฐานเรียบร้อยแล้ว',
                ]);
            }

            $userId = Auth::user();

            if ($userId->hasRole('user')) {
                // ส่งข้อมูลกลับไปยังหน้าแสดงผล
                return redirect()->route('dashboardkpi.user.show', $indicator->id)
                    ->with('success', 'บันทึกหลักฐานเรียบร้อยแล้ว');
            } else {
                // สำหรับผู้ดูแลระบบหรือบทบาทอื่น ๆ
                return redirect()->route('dashboardkpi.admin.show', $indicator->id)
                    ->with('success', 'บันทึกหลักฐานเรียบร้อยแล้ว');
            }
        } catch (\Throwable $e) {
            Log::error('Evidence store error', [
                'exception' => $e->getMessage(),
            ]);

            // rollback ลบไฟล์ที่อัปโหลดแล้วถ้าเกิด error
            if (!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $f) {
                    Storage::disk('public')->delete($f['path'] ?? null);
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง',
                ], 500);
            }

            return back()->withInput()->withErrors([
                'general' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง',
            ]);
        }
    }



    /**
     * คืน icon type ตาม MIME type
     */
    private function getFileTypeIcon($mimeType)
    {
        switch ($mimeType) {
            case 'application/pdf':
                return 'file-pdf';
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/png':
                return 'image';
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return 'file-text';
            default:
                return 'file';
        }
    }

    /**
     * Build a filesystem-safe slug for folder names with a reliable fallback.
     */
    private function safeFolderSegment(?string $text, string $fallback): string
    {
        $base = (string) ($text ?? '');
        $slug = trim(Str::slug($base, '-'));
        if ($slug === '' || $slug === '-') {
            return $fallback;
        }
        // Collapse duplicate separators just in case
        return preg_replace('/-+/', '-', $slug);
    }

    /**
     * แปลงขนาดไฟล์เป็น human-readable
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }




    /**
     * Display the specified evidence.
     */
    public function show($id): JsonResponse
    {
        try {
            $evidence = Evidence::with(['criteria', 'user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $evidence
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Evidence not found'
            ], 404);
        }
    }

    /**
     * Update the specified evidence in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'file' => 'sometimes|file',
            'type' => 'sometimes|required|string|max:100',
            'detail' => 'nullable|string',
            'status' => 'boolean',
            'criteria_id' => 'sometimes|required|exists:criteria,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $evidence = Evidence::findOrFail($id);

            $updateData = $request->only(['name', 'type', 'detail', 'status', 'criteria_id']);

            // Handle file upload if provided
            if ($request->hasFile('file')) {
                // Delete old file
                if ($evidence->path && Storage::disk('public')->exists($evidence->path)) {
                    Storage::disk('public')->delete($evidence->path);
                }

                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('evidence', $filename, 'public');
                $updateData['path'] = $path;
            }

            $evidence->update($updateData);
            $evidence->load(['criteria', 'user']);

            if (array_key_exists('detail', $updateData)) {
                $criteriaId = $updateData['criteria_id'] ?? $evidence->criteria_id;
                Criteria::where('id', $criteriaId)->update([
                    'report' => $updateData['detail'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Evidence updated successfully',
                'data' => $evidence
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update evidence',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified evidence from storage.
     */
    public function destroy($id)
    {
        try {
            $evidence = Evidence::findOrFail($id);

            // Delete associated files if they exist
            if (is_array($evidence->path) && isset($evidence->path['files'])) {
                foreach ($evidence->path['files'] as $file) {
                    if (!empty($file['path'])) {
                        Storage::disk('public')->delete($file['path']);
                    }
                }
            }

            $evidence->delete();

            // Flash success message to session
            return redirect()->back()->with('success', 'ลบหลักฐานเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            // Flash error message to session
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการลบหลักฐาน');
        }
    }

    public function preview($id)
    {
        $e = Evidence::findOrFail($id);
        $raw  = $e->getRawOriginal('path');
        $json = json_decode((string) $raw, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($json['files'][0])) {
            $file = $json['files'][0];
            $rel  = $this->normalizePath($file['path'] ?? null);

            if ($rel && Storage::disk('public')->exists($rel)) {
                $absolutePath = Storage::disk('public')->path($rel);
                $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

                // ✅ Convert office → pdf
                if (in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'])) {
                    $tempDir = storage_path('app/converted');
                    if (!is_dir($tempDir)) {
                        mkdir($tempDir, 0775, true);
                    }

                    $converted = $this->convertToPdf($absolutePath, $tempDir);
                    if ($converted) {
                        return response()->file($converted, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline; filename="' . $e->name . '.pdf"'
                        ]);
                    }
                    // Fallback to online viewers if configured
                    $viewer = strtolower((string) env('EVIDENCE_OFFICE_VIEWER', 'server'));
                    $publicUrl = asset('storage/' . ltrim($rel, '/'));
                    if (in_array($viewer, ['office_online', 'office', 'msoffice', 'online', 'auto'])) {
                        return redirect()->away('https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($publicUrl));
                    } elseif ($viewer === 'google' || $viewer === 'gdocs') {
                        return redirect()->away('https://docs.google.com/gview?embedded=1&url=' . urlencode($publicUrl));
                    }
                }

                // ✅ ถ้าเป็น pdf/image อยู่แล้ว
                return response()->file($absolutePath, [
                    'Content-Type' => $file['mime_type'] ?? $this->determineMime($absolutePath, null, $e->type ?? null),
                    'Content-Disposition' => 'inline; filename="' . $e->name . '"'
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }






    /**
     * Download evidence file.
     */


    public function download($id)
    {
        $e = Evidence::findOrFail($id);

        // ดึง JSON raw string โดยตรง
        $raw  = $e->getRawOriginal('path');
        $json = json_decode((string) $raw, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            $files = $json['files'] ?? [];
            $urls  = $json['urls'] ?? [];
            // --- มีไฟล์เดียว ---
            if (count($files) === 1) {
                $file = $files[0] ?? [];
                $rel  = $this->normalizePath($file['path'] ?? null);

                if ($rel && Storage::disk('public')->exists($rel)) {
                    $absolutePath = Storage::disk('public')->path($rel);
                    // Prefer renamed Evidence name for single-file entries, otherwise per-file custom/original
                    $downloadName = $e->name ?: ($file['custom_name'] ?? $file['original_name'] ?? basename($rel));
                    $downloadName = trim((string) $downloadName) !== '' ? (string) $downloadName : basename($rel);
                    // Ensure extension present
                    $nameExt = pathinfo($downloadName, PATHINFO_EXTENSION);
                    if ($nameExt === '' || $nameExt === null) {
                        $fallbackExt = $e->type ?? pathinfo($rel, PATHINFO_EXTENSION) ?? null;
                        if ($fallbackExt) {
                            $downloadName .= '.' . ltrim((string) $fallbackExt, '.');
                        }
                    }
                    $mime = $this->determineMime($absolutePath, $file['mime_type'] ?? null, $e->type ?? null);

                    if ($this->shouldOpenInline($mime, $e->type ?? null)) {
                        return response()->file($absolutePath, $this->inlineHeaders($downloadName, $mime));
                    }

                    return response()->download($absolutePath, $downloadName);
                }
                return $this->fileNotFound();
            }

            // --- หลายไฟล์ -> ZIP ---
            if (count($files) > 1) {
                $zipBase = Str::slug($e->name ?: "evidence-{$e->id}", '-') . "-{$e->id}";
                $tempDir = storage_path('app/temp');
                if (!is_dir($tempDir)) {
                    @mkdir($tempDir, 0775, true);
                }
                $zipFull = $tempDir . DIRECTORY_SEPARATOR . $zipBase . '.zip';

                $zip = new \ZipArchive();
                if ($zip->open($zipFull, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                    return response()->json(['success' => false, 'message' => 'ไม่สามารถสร้างไฟล์ ZIP ได้'], 500);
                }

                $used = [];
                foreach ($files as $f) {
                    $rel = $this->normalizePath($f['path'] ?? null);
                    if ($rel && Storage::disk('public')->exists($rel)) {
                        $entry = $f['custom_name'] ?? $f['original_name'] ?? basename($rel);
                        $entry = trim((string) $entry) !== '' ? (string) $entry : basename($rel);
                        $ext = pathinfo($entry, PATHINFO_EXTENSION);
                        if ($ext === '' || $ext === null) {
                            $fallbackExt = pathinfo($rel, PATHINFO_EXTENSION) ?? ($e->type ?? null);
                            if ($fallbackExt) {
                                $entry .= '.' . ltrim((string) $fallbackExt, '.');
                            }
                        }
                        if (isset($used[$entry])) {
                            $i = ++$used[$entry];
                            $nameOnly = pathinfo($entry, PATHINFO_FILENAME);
                            $extPart = pathinfo($entry, PATHINFO_EXTENSION);
                            $entry = $nameOnly . " (" . $i . ")." . $extPart;
                        } else {
                            $used[$entry] = 0;
                        }
                        $zip->addFile(Storage::disk('public')->path($rel), $entry);
                    }
                }
                $zip->close();

                return response()->download($zipFull, $zipBase . '.zip')->deleteFileAfterSend(true);
            }

            // --- มีแต่ URL ---
            if (!empty($urls)) {
                return redirect()->away($urls[0]);
            }

            return $this->fileNotFound();
        }

        // ====== เคส path string ======
        $path = $this->normalizePath((string) $raw);
        if ($path && Storage::disk('public')->exists($path)) {
            $full = Storage::disk('public')->path($path);
            $name = $e->name ?: basename($path);
            if (!str_contains($name, '.') && !empty($e->type)) {
                $name .= '.' . ltrim($e->type, '.');
            }

            $mime = $this->determineMime($full, null, $e->type ?? null);

            if ($this->shouldOpenInline($mime, $e->type ?? null)) {
                return response()->file($full, $this->inlineHeaders($name, $mime));
            }

            return response()->download($full, $name);
        }

        // ====== absolute path ======
        if ($raw && file_exists($raw)) {
            $downloadName = $e->name ?: basename($raw);
            $mime = $this->determineMime($raw, null, $e->type ?? null);

            if ($this->shouldOpenInline($mime, $e->type ?? null)) {
                return response()->file($raw, $this->inlineHeaders($downloadName, $mime));
            }

            return response()->download($raw, $downloadName);
        }

        return $this->fileNotFound();
    }

    /**
     * Normalize relative path ให้สะอาด
     */
    private function normalizePath(?string $path): ?string
    {
        if (!$path) return null;

        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        // ลบ prefix ที่ไม่จำเป็น เช่น storage/app/public/
        return preg_replace('#^(storage(?:/app)?/public/|public/|storage/)+#i', '', $path);
    }

    private function fileNotFound()
    {
        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }

    /**
     * Decide whether to display file inline (new tab) instead of download.
     * Inline for PDF and images only, per requirement.
     */
    private function shouldOpenInline(?string $mime, ?string $ext): bool
    {
        $mime = strtolower((string) ($mime ?? ''));
        $ext  = strtolower((string) ($ext ?? ''));

        if ($mime !== '') {
            if ($mime === 'application/pdf') return true;
            if (str_starts_with($mime, 'image/')) return true;
            if ($mime === 'image/svg+xml') return true;
            if ($mime === 'text/plain') return true;
            if ($mime === 'text/csv' || $mime === 'application/csv') return true;
            if ($mime === 'text/html') return true;
        }

        // Fallback by extension when MIME not available
        return in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'txt', 'csv', 'htm', 'html'], true);
    }
    private function convertToPdf(string $inputPath, string $outputDir): ?string
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        $soffice = $isWindows
            ? '"C:\\Program Files\\LibreOffice\\program\\soffice.exe"'
            : 'soffice';

        $command = $soffice . ' --headless --convert-to pdf --outdir '
            . escapeshellarg($outputDir) . ' ' . escapeshellarg($inputPath);

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $pdfPath = $outputDir . '/' . pathinfo($inputPath, PATHINFO_FILENAME) . '.pdf';
            return file_exists($pdfPath) ? $pdfPath : null;
        }

        return null;
    }

    /**
     * Resolve MIME type with fallbacks based on file and extension.
     */
    private function determineMime(string $path, ?string $providedMime, ?string $ext): ?string
    {
        $providedMime = $providedMime ? strtolower($providedMime) : null;
        if ($providedMime) {
            return $providedMime;
        }

        $detected = null;
        if (function_exists('mime_content_type')) {
            try {
                $detected = @mime_content_type($path) ?: null;
            } catch (\Throwable $__) {
                $detected = null;
            }
        }
        if ($detected) return strtolower($detected);

        $ext = strtolower((string) ($ext ?? pathinfo($path, PATHINFO_EXTENSION)));
        $map = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'bmp' => 'image/bmp',
            'svg' => 'image/svg+xml',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'htm' => 'text/html',
            'html' => 'text/html',
        ];
        return $map[$ext] ?? null;
    }

    /**
     * Build headers for inline display with safe filename and proper encoding.
     */
    private function inlineHeaders(string $filename, ?string $mime): array
    {
        $headers = [];
        if ($mime) $headers['Content-Type'] = $mime;
        $headers['X-Content-Type-Options'] = 'nosniff';
        $ascii = $this->asciiFilename($filename);
        $utf = rawurlencode($filename);
        $headers['Content-Disposition'] = "inline; filename=\"{$ascii}\"; filename*=UTF-8''{$utf}";
        return $headers;
    }

    private function asciiFilename(string $name): string
    {
        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
        return $safe !== null && $safe !== '' ? $safe : 'file';
    }
    public function getByCriteria($criteriaId): JsonResponse
    {
        try {
            $evidences = Evidence::with(['user'])
                ->where('criteria_id', $criteriaId)
                ->where('status', true)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $evidences
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get evidence',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle evidence status.
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $evidence = Evidence::findOrFail($id);
            $evidence->update(['status' => !$evidence->status]);
            $evidence->load(['criteria', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Evidence status updated successfully',
                'data' => $evidence
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update evidence status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
