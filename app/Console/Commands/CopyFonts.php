<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyFonts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fonts:copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy fonts from public/fonts to storage/fonts for Dompdf';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $source = public_path('fonts');
        $destination = storage_path('fonts');

        if (!File::exists($source)) {
            $this->error("ไม่พบโฟลเดอร์ต้นทาง: {$source}");
            return self::FAILURE;
        }

        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        foreach (File::files($source) as $file) {
            File::copy($file->getRealPath(), $destination . '/' . $file->getFilename());
        }

        $this->info("คัดลอกฟอนต์จาก {$source} ไปยัง {$destination} แล้ว");
        return self::SUCCESS;
    }
}

