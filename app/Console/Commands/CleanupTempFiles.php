<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:cleanup-temp {--hours=1 : Delete files older than this many hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old temporary files from QR code generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tempDir = storage_path('app/temp');
        $hours = (int) $this->option('hours');
        $cutoffTime = time() - ($hours * 3600);

        if (!File::exists($tempDir)) {
            $this->info('Temp directory does not exist. Nothing to clean.');
            return 0;
        }

        $files = File::files($tempDir);
        $deletedCount = 0;
        $totalSize = 0;

        foreach ($files as $file) {
            if ($file->getMTime() < $cutoffTime) {
                $size = $file->getSize();
                if (File::delete($file->getPathname())) {
                    $deletedCount++;
                    $totalSize += $size;
                }
            }
        }

        // Clean up empty temp directory if no files remain
        if (empty(File::files($tempDir))) {
            try {
                File::deleteDirectory($tempDir);
                $this->info('Removed empty temp directory.');
            } catch (\Exception $e) {
                // Directory might not be empty or permission issue, ignore
            }
        }

        if ($deletedCount > 0) {
            $sizeMB = number_format($totalSize / 1024 / 1024, 2);
            $this->info("Cleaned up {$deletedCount} temporary file(s) ({$sizeMB} MB) older than {$hours} hour(s).");
        } else {
            $this->info('No old temporary files to clean up.');
        }

        return 0;
    }
}

