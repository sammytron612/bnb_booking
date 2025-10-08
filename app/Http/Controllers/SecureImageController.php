<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\PropertyImage;

class SecureImageController extends Controller
{
    /**
     * Serve property images securely with access control
     */
    public function servePropertyImage(Request $request, $filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning('Invalid filename attempted', ['filename' => $filename, 'ip' => $request->ip()]);
                return response()->json(['error' => 'Invalid file'], 400);
            }

            // Check if image exists in database (additional security layer)
            $image = PropertyImage::where('location', 'LIKE', '%' . $filename)->first();

            if (!$image) {
                Log::info('Image not found in database', ['filename' => $filename]);
                return response()->json(['error' => 'Image not found'], 404);
            }

            // Construct file path for private storage
            $path = 'property-images/' . $filename;

            if (!Storage::disk('private')->exists($path)) {
                Log::warning('Physical file not found', ['path' => $path]);
                return response()->json(['error' => 'File not found'], 404);
            }

            // Get file contents and MIME type
            $file = Storage::disk('private')->get($path);
            $fullPath = Storage::disk('private')->path($path);
            $mimeType = mime_content_type($fullPath);

            // Security: Validate MIME type before serving
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                Log::warning('Attempted to serve non-image file', ['filename' => $filename, 'mime' => $mimeType]);
                return response()->json(['error' => 'Invalid file type'], 400);
            }

            return response($file)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('X-Content-Type-Options', 'nosniff')
                ->header('Content-Security-Policy', "default-src 'none'");

        } catch (\Exception $e) {
            Log::error('Error serving image', [
                'filename' => $filename,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Serve images for public viewing (venues, etc.)
     */
    public function servePublicImage(Request $request, $filename)
    {
        // This endpoint has less strict requirements but still validates files
        try {
            if (!$this->isValidFilename($filename)) {
                return response()->json(['error' => 'Invalid file'], 400);
            }

            $path = 'property-images/' . $filename;

            if (!Storage::disk('private')->exists($path)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $file = Storage::disk('private')->get($path);
            $fullPath = Storage::disk('private')->path($path);
            $mimeType = mime_content_type($fullPath);

            // Validate MIME type
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                return response()->json(['error' => 'Invalid file type'], 400);
            }

            return response($file)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=86400') // 24 hours for public images
                ->header('X-Content-Type-Options', 'nosniff')
                ->header('Content-Security-Policy', "default-src 'none'");

        } catch (\Exception $e) {
            Log::error('Error serving public image', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Validate filename to prevent directory traversal and other attacks
     */
    private function isValidFilename($filename)
    {
        // Check for directory traversal attempts
        if (strpos($filename, '..') !== false ||
            strpos($filename, '/') !== false ||
            strpos($filename, '\\') !== false) {
            return false;
        }

        // Check for valid filename pattern
        return preg_match('/^[a-zA-Z0-9._-]+\.(jpg|jpeg|png|gif|webp)$/i', $filename);
    }
}
