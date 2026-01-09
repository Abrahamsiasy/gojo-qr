<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Illuminate\Http\UploadedFile;

class LogoOptimizerService
{
    protected ImageManager $manager;
    protected int $maxSize;

    public function __construct()
    {
        // Try to use Imagick if available, otherwise fall back to GD
        try {
            if (extension_loaded('imagick')) {
                $this->manager = new ImageManager(new ImagickDriver());
            } else {
                $this->manager = new ImageManager(new Driver());
            }
        } catch (\Exception $e) {
            $this->manager = new ImageManager(new Driver());
        }
        $this->maxSize = 200; // Max size in pixels (30% of 512px QR code)
    }

    /**
     * Optimize and prepare logo for QR code merging
     * Returns the optimized image as a temporary file path
     */
    public function optimize(UploadedFile $file, int $qrSize = 512, float $sizePercentage = 0.3): string
    {
        // Calculate logo size based on percentage (default 30% of QR code size)
        $logoSize = (int) ($qrSize * $sizePercentage);
        
        try {
            // Read the image
            $image = $this->manager->read($file->getRealPath());
            
            // Get dimensions
            $width = $image->width();
            $height = $image->height();
            
            // Calculate new dimensions while maintaining aspect ratio
            if ($width > $height) {
                $newWidth = min($logoSize, $width);
                $newHeight = (int) (($height / $width) * $newWidth);
            } else {
                $newHeight = min($logoSize, $height);
                $newWidth = (int) (($width / $height) * $newHeight);
            }
            
            // Resize the image maintaining aspect ratio
            $image->scale($newWidth, $newHeight);
            
            // Create temporary file path in storage/app/temp (create directory if needed)
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }
            
            $tempPath = $tempDir . DIRECTORY_SEPARATOR . 'qr_logo_' . uniqid() . '.png';
            
            // Ensure PNG format with transparency support
            $pngData = $image->toPng();
            $pngData->save($tempPath);
            
            // Verify file was saved
            if (!file_exists($tempPath)) {
                throw new \Exception('Failed to save optimized logo');
            }
            
            return $tempPath;
        } catch (\Exception $e) {
            // If image processing fails, return the original file path as fallback
            // This ensures QR code generation continues even if logo optimization fails
            return $file->getRealPath();
        }
    }

    /**
     * Clean up temporary file
     */
    public function cleanup(string $filePath): void
    {
        // Delete if it's a temporary file (in storage/app/temp or system temp)
        if (file_exists($filePath)) {
            $tempDir = storage_path('app/temp');
            $sysTempDir = sys_get_temp_dir();
            
            if (strpos($filePath, $tempDir) === 0 || strpos($filePath, $sysTempDir) === 0) {
                @unlink($filePath);
            }
        }
    }
}

