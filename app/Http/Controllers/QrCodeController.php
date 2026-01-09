<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\LogoOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
            'content' => 'required|string|max:5000',
            'size' => 'nullable|integer|min:200|max:1000',
            'format' => 'nullable|in:png,svg',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'logo_size' => 'nullable|numeric|min:0.1|max:0.4',
            'error_correction' => 'nullable|in:L,M,Q,H',
            'color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/i'],
            'background_color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/i'],
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
        if ($request->hasFile('logo') && $format === 'png') {
            try {
                $tempLogoPath = $this->logoOptimizer->optimize(
                    $request->file('logo'),
                    $size
                );
                
                // Verify the temp file exists and is readable
                if ($tempLogoPath && file_exists($tempLogoPath) && is_readable($tempLogoPath)) {
                    // Ensure we have an absolute path
                    $absolutePath = realpath($tempLogoPath);
                    
                    if ($absolutePath && file_exists($absolutePath)) {
                        // Adjust logo size based on user preference (0.3 = 30%)
                        // The merge method accepts absolute path
                        $qrCode->merge($absolutePath, $logoSize);
                    } else {
                        Log::warning('Logo file not found at path: ' . $tempLogoPath);
                    }
                } else {
                    Log::warning('Logo file is not readable: ' . ($tempLogoPath ?? 'null'));
                }
            } catch (\Exception $e) {
                // If logo processing fails, continue without logo
                // Log error for debugging but don't break QR generation
                Log::warning('Logo merge failed: ' . $e->getMessage());
                if ($tempLogoPath && file_exists($tempLogoPath)) {
                    $this->logoOptimizer->cleanup($tempLogoPath);
                    $tempLogoPath = null;
                }
            }
        }

        // Generate QR code
        $qrData = $qrCode->generate($qrContent);

        // Cleanup temporary logo file
        if ($tempLogoPath) {
            $this->logoOptimizer->cleanup($tempLogoPath);
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
