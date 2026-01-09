<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\LogoOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class QrCodeController extends Controller
{
    protected LogoOptimizerService $logoOptimizer;

    public function __construct(LogoOptimizerService $logoOptimizer)
    {
        $this->logoOptimizer = $logoOptimizer;
    }

    /**
     * Show the main QR code generator page
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Generate QR code
     */
    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:url,text,email,sms,wifi,vcard,phone',
            'content' => 'required|string|max:3000', // Reduced from 5000 to prevent abuse
            'size' => 'nullable|integer|min:200|max:1000',
            'format' => 'nullable|in:png,svg',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'logo_size' => 'nullable|numeric|min:0.1|max:0.4',
            'error_correction' => 'nullable|in:L,M,Q,H',
            'color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/i'],
            'background_color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/i'],
            'enable_scan_text' => 'nullable|boolean',
            'scan_text' => 'nullable|string|max:50',
            'scan_text_style' => 'nullable|in:plain,speech,banner,badge',
            'scan_text_size' => 'nullable|in:small,medium,large,xlarge',
            'scan_text_position' => 'nullable|in:above,below',
            'scan_text_color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/i'],
            'scan_bg_color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/i'],
            'enable_border' => 'nullable|boolean',
            'border_style' => 'nullable|in:square,rounded,circle',
            'border_width' => 'nullable|integer|min:10|max:50',
            'border_gap' => 'nullable|integer|min:5|max:30',
        ]);

        $type = $request->input('type');
        $content = $request->input('content');
        $size = (int) ($request->input('size', 512));
        $format = $request->input('format', 'png');
        $errorCorrection = $request->input('error_correction', 'H');
        $logoSize = (float) ($request->input('logo_size', 0.3));

        // Build QR code content based on type
        $qrContent = $this->buildQrContent($type, $content);

        // Start building QR code
        $qrCode = QrCode::size($size)
            ->format($format)
            ->style('square')
            ->errorCorrection($errorCorrection);

        // Set colors if provided
        if ($request->filled('color')) {
            $color = $this->hexToRgb($request->input('color'));
            $qrCode->color($color[0], $color[1], $color[2]);
        }

        if ($request->filled('background_color') && $format === 'png') {
            $bgColor = $this->hexToRgb($request->input('background_color'));
            $qrCode->backgroundColor($bgColor[0], $bgColor[1], $bgColor[2]);
        }

        // Handle logo upload (only for PNG format, SVG doesn't support logo merging)
        $tempLogoPath = null;
        if ($request->hasFile('logo') && $format === 'png' && $request->has('enable_logo') && $request->boolean('enable_logo')) {
            try {
                // Optimize logo with user's size preference
                $tempLogoPath = $this->logoOptimizer->optimize(
                    $request->file('logo'),
                    $size,
                    $logoSize
                );
                
                // Verify the temp file exists and is readable
                if ($tempLogoPath && file_exists($tempLogoPath) && is_readable($tempLogoPath)) {
                    // Ensure we have an absolute path
                    $absolutePath = realpath($tempLogoPath);
                    
                    if ($absolutePath && file_exists($absolutePath)) {
                        // The merge method accepts absolute path and size ratio
                        $qrCode->merge($absolutePath, $logoSize);
                    } else {
                        Log::warning('Logo file not found at path: ' . $tempLogoPath);
                    }
                } else {
                    Log::warning('Logo file is not readable: ' . ($tempLogoPath ?? 'null'));
                }
            } catch (\Exception $e) {
                // If logo processing fails, continue without logo
                Log::warning('Logo merge failed: ' . $e->getMessage());
                if ($tempLogoPath && file_exists($tempLogoPath)) {
                    $this->logoOptimizer->cleanup($tempLogoPath);
                    $tempLogoPath = null;
                }
            }
        }

        // Generate QR code
        $qrData = $qrCode->generate($qrContent);

        // Cleanup temporary logo file (always cleanup, even on errors)
        try {
            if ($tempLogoPath && file_exists($tempLogoPath)) {
                $this->logoOptimizer->cleanup($tempLogoPath);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to cleanup logo file: ' . $e->getMessage());
        }

        // Add border if enabled (only for PNG format)
        if ($request->has('enable_border') && $request->boolean('enable_border') && $format === 'png') {
            try {
                $qrData = $this->addBorder($qrData, $request, $size);
            } catch (\Exception $e) {
                Log::warning('Failed to add border: ' . $e->getMessage());
                // Continue without border if it fails
            }
        }

        // Add "Scan Me" text if enabled (only for PNG format)
        if ($request->has('enable_scan_text') && $request->boolean('enable_scan_text') && $format === 'png') {
            try {
                $qrData = $this->addScanText($qrData, $request, $size);
            } catch (\Exception $e) {
                Log::warning('Failed to add scan text: ' . $e->getMessage());
                // Continue without scan text if it fails
            }
        }

        // Return response
        $contentType = $format === 'svg' ? 'image/svg+xml' : 'image/png';
        $filename = 'qrcode-' . Str::slug($type) . '-' . time() . '.' . $format;

        return response($qrData)
            ->header('Content-Type', $contentType)
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Add border/frame around QR code
     */
    protected function addBorder(string $qrData, Request $request, int $qrSize): string
    {
        $style = $request->input('border_style', 'square');
        $width = (int) $request->input('border_width', 20);
        $gap = (int) $request->input('border_gap', 10);
        
        // Use QR code color for border (or default to black if not set)
        $qrColorHex = $request->input('color', '#000000');
        $borderColor = $this->hexToRgb($qrColorHex);
        $bgColor = $this->hexToRgb($request->input('background_color', '#ffffff'));
        
        try {
            // Create GD image from binary QR data
            $qrGdImage = @imagecreatefromstring($qrData);
            if (!$qrGdImage) {
                throw new \Exception('Failed to create image from QR code data');
            }
            
            // Get QR code dimensions
            $qrWidth = imagesx($qrGdImage);
            $qrHeight = imagesy($qrGdImage);
            
            // Layout: Border at edge, then gap, then QR code
            // Structure from outside in: Border (width px) -> Gap (gap px) -> QR code
            // Total padding per side = border width + gap
            $totalPadding = $width + $gap;
            $newWidth = $qrWidth + ($totalPadding * 2);
            $newHeight = $qrHeight + ($totalPadding * 2);
            
            // Create new GD image
            $gdImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Set background color
            $bgColorRes = imagecolorallocate($gdImage, $bgColor[0], $bgColor[1], $bgColor[2]);
            imagefill($gdImage, 0, 0, $bgColorRes);
            
            // Position QR code: after border + gap from edge
            $qrOffsetX = $totalPadding; // border width + gap
            $qrOffsetY = $totalPadding;
            imagecopyresampled($gdImage, $qrGdImage, $qrOffsetX, $qrOffsetY, 0, 0, $qrWidth, $qrHeight, $qrWidth, $qrHeight);
            
            // Clean up original QR image
            imagedestroy($qrGdImage);
            
            // Draw border based on style
            // Border is drawn at image edge (0 to width pixels inward)
            $borderColorRes = imagecolorallocate($gdImage, $borderColor[0], $borderColor[1], $borderColor[2]);
            $cornerRadius = min(30, $width * 2);
            
            // Border outer edge is at image edge (0,0 to newWidth, newHeight)
            // Border inner edge is width pixels inward (creates border thickness)
            // Gap is between border inner edge and QR code
            $borderOuterX1 = 0;
            $borderOuterY1 = 0;
            $borderOuterX2 = $newWidth - 1;
            $borderOuterY2 = $newHeight - 1;
            
            switch ($style) {
                case 'rounded':
                    // Draw rounded rectangle border frame
                    $this->drawRoundedRectangleFrame($gdImage, $borderOuterX1, $borderOuterY1, $borderOuterX2, $borderOuterY2, $cornerRadius, $width, $borderColorRes, $bgColorRes);
                    break;
                    
                case 'circle':
                    // Draw circular border (if square QR code)
                    if ($qrWidth === $qrHeight) {
                        $centerX = $newWidth / 2;
                        $centerY = $newHeight / 2;
                        // Border outer radius: from center to border outer edge
                        $outerRadius = ($newWidth / 2) - $width;
                        // Border inner radius: outer radius - border width
                        $innerRadius = $outerRadius - $width;
                        
                        // But we want gap between QR and border, so adjust:
                        // QR code center is at image center
                        // Border inner edge should be gap distance from QR edge
                        $qrRadius = $qrWidth / 2;
                        $innerRadius = $qrRadius + $gap; // Gap from QR edge
                        $outerRadius = $innerRadius + $width; // Border thickness
                        
                        // Draw outer circle (border)
                        imagefilledellipse($gdImage, (int)$centerX, (int)$centerY, (int)($outerRadius * 2), (int)($outerRadius * 2), $borderColorRes);
                        // Cut out inner circle with background color (creates gap)
                        imagefilledellipse($gdImage, (int)$centerX, (int)$centerY, (int)($innerRadius * 2), (int)($innerRadius * 2), $bgColorRes);
                    } else {
                        // Fallback to rounded rectangle
                        $this->drawRoundedRectangleFrame($gdImage, $borderOuterX1, $borderOuterY1, $borderOuterX2, $borderOuterY2, $cornerRadius, $width, $borderColorRes, $bgColorRes);
                    }
                    break;
                    
                case 'square':
                default:
                    // Draw square border frame at image edge
                    // Border is drawn from image edge (0,0) inward by width pixels
                    for ($i = 0; $i < $width; $i++) {
                        imagerectangle(
                            $gdImage, 
                            $i, 
                            $i, 
                            $newWidth - 1 - $i, 
                            $newHeight - 1 - $i, 
                            $borderColorRes
                        );
                    }
                    break;
            }
            
            // Output image to string
            ob_start();
            imagepng($gdImage);
            $imageData = ob_get_clean();
            imagedestroy($gdImage);
            
            return $imageData;
        } catch (\Exception $e) {
            Log::warning('Border drawing failed: ' . $e->getMessage());
            return $qrData; // Return original if border fails
        }
    }
    
    /**
     * Draw rounded rectangle frame (helper for border)
     */
    protected function drawRoundedRectangleFrame($image, $x1, $y1, $x2, $y2, $cornerRadius, $borderWidth, $borderColor, $bgColor)
    {
        // Draw outer rounded rectangle
        $this->drawRoundedRectangle($image, $x1, $y1, $x2, $y2, $cornerRadius, $borderColor);
        
        // Draw inner rounded rectangle with background color to create frame effect
        $innerX1 = $x1 + $borderWidth;
        $innerY1 = $y1 + $borderWidth;
        $innerX2 = $x2 - $borderWidth;
        $innerY2 = $y2 - $borderWidth;
        $innerRadius = max(0, $cornerRadius - $borderWidth);
        
        $this->drawRoundedRectangle($image, $innerX1, $innerY1, $innerX2, $innerY2, $innerRadius, $bgColor);
    }
    
    /**
     * Draw filled rounded rectangle (helper)
     */
    protected function drawRoundedRectangle($image, $x1, $y1, $x2, $y2, $radius, $color)
    {
        $radius = min($radius, min(($x2 - $x1) / 2, ($y2 - $y1) / 2));
        
        // Draw rounded corners
        imagefilledellipse($image, (int)($x1 + $radius), (int)($y1 + $radius), (int)($radius * 2), (int)($radius * 2), $color);
        imagefilledellipse($image, (int)($x2 - $radius), (int)($y1 + $radius), (int)($radius * 2), (int)($radius * 2), $color);
        imagefilledellipse($image, (int)($x1 + $radius), (int)($y2 - $radius), (int)($radius * 2), (int)($radius * 2), $color);
        imagefilledellipse($image, (int)($x2 - $radius), (int)($y2 - $radius), (int)($radius * 2), (int)($radius * 2), $color);
        
        // Draw rectangle sides
        imagefilledrectangle($image, (int)($x1 + $radius), (int)$y1, (int)($x2 - $radius), (int)$y2, $color);
        imagefilledrectangle($image, (int)$x1, (int)($y1 + $radius), (int)$x2, (int)($y2 - $radius), $color);
    }

    /**
     * Add "Scan Me" text with different visual styles
     */
    protected function addScanText(string $qrData, Request $request, int $qrSize): string
    {
        $text = strtoupper($request->input('scan_text', 'SCAN ME'));
        $style = $request->input('scan_text_style', 'speech');
        $sizeOption = $request->input('scan_text_size', 'medium');
        $position = $request->input('scan_text_position', 'below');
        $textColorHex = $request->input('scan_text_color', '#000000');
        $bgColorHex = $request->input('scan_bg_color', '#000000');
        
        // Convert hex to RGB
        $textColor = $this->hexToRgb($textColorHex);
        $bgColor = $this->hexToRgb($bgColorHex);
        $qrBgColor = $this->hexToRgb($request->input('background_color', '#ffffff'));
        
        // Calculate font size based on QR size
        $fontSizes = [
            'small' => max(14, (int) ($qrSize * 0.035)),
            'medium' => max(18, (int) ($qrSize * 0.045)),
            'large' => max(22, (int) ($qrSize * 0.055)),
            'xlarge' => max(28, (int) ($qrSize * 0.065)),
        ];
        $fontSize = $fontSizes[$sizeOption] ?? $fontSizes['medium'];
        
        // Use GD directly to work with binary QR code data
        try {
            // Create GD image from binary QR data
            $qrGdImage = @imagecreatefromstring($qrData);
            if (!$qrGdImage) {
                throw new \Exception('Failed to create image from QR code data');
            }
            
            // Get QR code dimensions
            $qrWidth = imagesx($qrGdImage);
            $qrHeight = imagesy($qrGdImage);
            
            // Calculate dimensions for text area
            $padding = max(12, (int) ($qrSize * 0.08));
            $textAreaHeight = (int) ($fontSize * 1.8);
            
            // Create new image with space for text
            if ($position === 'above') {
                $newHeight = $qrHeight + $textAreaHeight + $padding;
                $qrOffsetY = $textAreaHeight + ($padding / 2);
                $textOffsetY = $padding / 2;
            } else {
                $newHeight = $qrHeight + $textAreaHeight + $padding;
                $qrOffsetY = 0;
                $textOffsetY = $qrHeight + ($padding / 2);
            }
            
            // Create new GD image
            $gdImage = imagecreatetruecolor($qrWidth, $newHeight);
            
            // Set background color
            $bgColorRes = imagecolorallocate($gdImage, $qrBgColor[0], $qrBgColor[1], $qrBgColor[2]);
            imagefill($gdImage, 0, 0, $bgColorRes);
            
            // Copy QR code to new image
            imagecopyresampled($gdImage, $qrGdImage, 0, (int)$qrOffsetY, 0, 0, $qrWidth, $qrHeight, $qrWidth, $qrHeight);
            
            // Clean up original QR image
            imagedestroy($qrGdImage);
        
        // Calculate text dimensions
        $font = 5; // Built-in font
        $charWidth = imagefontwidth($font);
        $charHeight = imagefontheight($font);
        $textWidth = $charWidth * strlen($text);
        $textX = (int)(($qrWidth - $textWidth) / 2);
        $textY = (int)$textOffsetY;
        
        // Draw based on style
        switch ($style) {
            case 'speech':
                // Speech bubble style
                $bubblePadding = max(12, (int)($fontSize * 0.4));
                $bubbleWidth = $textWidth + ($bubblePadding * 2);
                $bubbleHeight = $charHeight + ($bubblePadding * 2);
                $bubbleX = (int)(($qrWidth - $bubbleWidth) / 2);
                $bubbleY = $textY - $bubblePadding;
                
                // Adjust bubble position if text is above QR code
                if ($position === 'above') {
                    $bubbleY = max(5, $textY - $bubblePadding);
                }
                
                $bgColorRes = imagecolorallocate($gdImage, $bgColor[0], $bgColor[1], $bgColor[2]);
                $textColorRes = imagecolorallocate($gdImage, $textColor[0], $textColor[1], $textColor[2]);
                
                // Draw bubble background
                imagefilledrectangle($gdImage, $bubbleX, $bubbleY, $bubbleX + $bubbleWidth, $bubbleY + $bubbleHeight, $bgColorRes);
                
                // Draw bubble border
                imagerectangle($gdImage, $bubbleX, $bubbleY, $bubbleX + $bubbleWidth, $bubbleY + $bubbleHeight, $bgColorRes);
                
                // Draw pointer triangle pointing towards QR code
                $pointerX = (int)($qrWidth / 2);
                $pointerSize = 12;
                
                if ($position === 'above') {
                    // Pointer points down to QR code
                    $pointerY = $bubbleY + $bubbleHeight;
                    imagefilledpolygon($gdImage, [
                        $pointerX - $pointerSize, $pointerY,
                        $pointerX + $pointerSize, $pointerY,
                        $pointerX, $pointerY + $pointerSize
                    ], 3, $bgColorRes);
                } else {
                    // Pointer points up to QR code
                    $pointerY = $bubbleY;
                    imagefilledpolygon($gdImage, [
                        $pointerX - $pointerSize, $pointerY,
                        $pointerX + $pointerSize, $pointerY,
                        $pointerX, $pointerY - $pointerSize
                    ], 3, $bgColorRes);
                }
                
                // Draw text centered in bubble
                imagestring($gdImage, $font, $textX, $textY, $text, $textColorRes);
                break;
                
            case 'banner':
                // Banner/ribbon style
                $bannerHeight = $charHeight + max(16, (int)($fontSize * 0.5));
                $bannerY = max(0, $textY - ($bannerHeight / 2));
                
                $bgColorRes = imagecolorallocate($gdImage, $bgColor[0], $bgColor[1], $bgColor[2]);
                $darkBgColor = imagecolorallocate($gdImage, max(0, $bgColor[0] - 20), max(0, $bgColor[1] - 20), max(0, $bgColor[2] - 20));
                $textColorRes = imagecolorallocate($gdImage, $textColor[0], $textColor[1], $textColor[2]);
                
                // Draw banner background
                imagefilledrectangle($gdImage, 0, $bannerY, $qrWidth, $bannerY + $bannerHeight, $bgColorRes);
                
                // Draw banner edges (folded effect)
                $foldSize = min(25, (int)($qrWidth * 0.05));
                imagefilledpolygon($gdImage, [
                    0, $bannerY,
                    $foldSize, $bannerY + 4,
                    $foldSize, $bannerY + $bannerHeight - 4,
                    0, $bannerY + $bannerHeight
                ], 4, $darkBgColor);
                
                imagefilledpolygon($gdImage, [
                    $qrWidth, $bannerY,
                    $qrWidth - $foldSize, $bannerY + 4,
                    $qrWidth - $foldSize, $bannerY + $bannerHeight - 4,
                    $qrWidth, $bannerY + $bannerHeight
                ], 4, $darkBgColor);
                
                // Draw text
                imagestring($gdImage, $font, $textX, $textY, $text, $textColorRes);
                break;
                
            case 'badge':
                // Badge/label style
                $badgePadding = max(10, (int)($fontSize * 0.35));
                $badgeWidth = $textWidth + ($badgePadding * 2);
                $badgeHeight = $charHeight + ($badgePadding * 2);
                $badgeX = (int)(($qrWidth - $badgeWidth) / 2);
                $badgeY = max(0, $textY - $badgePadding);
                
                $bgColorRes = imagecolorallocate($gdImage, $bgColor[0], $bgColor[1], $bgColor[2]);
                $borderColor = imagecolorallocate($gdImage, max(0, $bgColor[0] - 30), max(0, $bgColor[1] - 30), max(0, $bgColor[2] - 30));
                $textColorRes = imagecolorallocate($gdImage, $textColor[0], $textColor[1], $textColor[2]);
                
                // Draw badge background
                imagefilledrectangle($gdImage, $badgeX, $badgeY, $badgeX + $badgeWidth, $badgeY + $badgeHeight, $bgColorRes);
                
                // Draw border
                imagerectangle($gdImage, $badgeX, $badgeY, $badgeX + $badgeWidth, $badgeY + $badgeHeight, $borderColor);
                
                // Draw text
                imagestring($gdImage, $font, $textX, $textY, $text, $textColorRes);
                break;
                
            case 'plain':
            default:
                // Plain text style
                $textColorRes = imagecolorallocate($gdImage, $textColor[0], $textColor[1], $textColor[2]);
                imagestring($gdImage, $font, $textX, $textY, $text, $textColorRes);
                break;
        }
        
        // Output to string
        ob_start();
        imagepng($gdImage);
        $finalImage = ob_get_clean();
        imagedestroy($gdImage);
        
        return $finalImage;
        } catch (\Exception $e) {
            // Cleanup on error
            if (isset($qrGdImage)) {
                @imagedestroy($qrGdImage);
            }
            if (isset($gdImage)) {
                @imagedestroy($gdImage);
            }
            throw $e;
        }
    }

    /**
     * Build QR code content based on type
     */
    protected function buildQrContent(string $type, string $content): string
    {
        return match ($type) {
            'url' => $content,
            'text' => $content,
            'email' => "mailto:{$content}",
            'sms' => "sms:{$content}",
            'phone' => "tel:{$content}",
            'wifi' => $this->buildWifiString($content),
            'vcard' => $this->buildVCardString($content),
            default => $content,
        };
    }

    /**
     * Build WiFi connection string
     * Expected format: SSID:Password:SecurityType (e.g., "MyWiFi:password123:WPA")
     */
    protected function buildWifiString(string $content): string
    {
        $parts = explode(':', $content);
        $ssid = $parts[0] ?? '';
        $password = $parts[1] ?? '';
        $security = strtoupper($parts[2] ?? 'WPA');

        return "WIFI:T:{$security};S:{$ssid};P:{$password};;";
    }

    /**
     * Build vCard string
     * Expected format: Name:Phone:Email:Organization (pipe-separated)
     */
    protected function buildVCardString(string $content): string
    {
        $parts = explode('|', $content);
        $name = $parts[0] ?? '';
        $phone = $parts[1] ?? '';
        $email = $parts[2] ?? '';
        $org = $parts[3] ?? '';

        $vcard = "BEGIN:VCARD\n";
        $vcard .= "VERSION:3.0\n";
        if ($name) $vcard .= "FN:{$name}\n";
        if ($phone) $vcard .= "TEL:{$phone}\n";
        if ($email) $vcard .= "EMAIL:{$email}\n";
        if ($org) $vcard .= "ORG:{$org}\n";
        $vcard .= "END:VCARD";

        return $vcard;
    }

    /**
     * Convert hex color to RGB array
     */
    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
