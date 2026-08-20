<?php

use App\Support\RichTextSanitizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $sanitizer = app(RichTextSanitizer::class);

        $this->sanitizeTable($sanitizer, 'indicators', ['description', 'condition', 'comment', 'annotation']);
        $this->sanitizeTable($sanitizer, 'criterias', ['description', 'report', 'evidence_comment']);
        $this->sanitizeTable($sanitizer, 'evidence', ['detail']);
        $this->sanitizeTable($sanitizer, 'sar_reports', ['section1', 'section2', 'section4']);
    }

    public function down(): void
    {
        // Sanitization is intentionally irreversible.
    }

    private function sanitizeTable(RichTextSanitizer $sanitizer, string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $columns = array_values(array_filter(
            $columns,
            fn (string $column): bool => Schema::hasColumn($table, $column),
        ));

        if ($columns === []) {
            return;
        }

        DB::table($table)
            ->select(array_merge(['id'], $columns))
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($sanitizer, $table, $columns): void {
                foreach ($rows as $row) {
                    $updates = [];
                    foreach ($columns as $column) {
                        $original = $row->{$column};
                        $sanitized = $sanitizer->sanitize($original);
                        if ($sanitized !== $original) {
                            $updates[$column] = $sanitized;
                        }
                    }

                    if ($updates !== []) {
                        DB::table($table)->where('id', $row->id)->update($updates);
                    }
                }
            });
    }
};
