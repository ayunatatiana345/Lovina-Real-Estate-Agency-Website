<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocationImageService
{
    /**
     * Maximum width and height for location photos to preserve web performance.
     */
    protected int $maxDimension = 2048;

    /**
     * WebP output compression quality (0-100).
     */
    protected int $webpQuality = 85;

    /**
     * Process an uploaded location photo:
     * - Generates an SEO-friendly deterministic filename from the Location Name:
     *   e.g. "Lovina" -> "lovina-location-01.webp"
     *        "Celuk Buluh" -> "celuk-buluh-location-01.webp"
     *        "Sing-Sing" -> "sing-sing-location-01.webp"
     * - Auto-rotates using EXIF orientation if applicable
     * - Scales down large phone/camera images while strictly preserving aspect ratio
     * - Converts to WebP format
     * - Stores in the public locations storage directory (locations/)
     *
     * @param UploadedFile $file
     * @param string $locationName
     * @param int $sequence
     * @return string|null Relative storage path (e.g. "locations/lovina-location-01.webp") or null on failure
     */
    public function processAndStore(UploadedFile $file, string $locationName, int $sequence = 1): ?string
    {
        try {
            // 1. Generate clean, readable, SEO-friendly location slug
            $cleanName = Str::slug($locationName);
            if (empty($cleanName)) {
                $cleanName = 'location';
            }

            // 2. SEO-friendly filename with unique hash: {location-slug}-north-bali-location-{hash}.webp
            $uniqueSuffix = substr(bin2hex(random_bytes(4)), 0, 4);
            $filename = "{$cleanName}-north-bali-location-{$uniqueSuffix}.webp";

            $directory = 'locations';
            $relativeStoragePath = "{$directory}/{$filename}";

            $disk = Storage::disk('public');
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            $absoluteDestinationPath = $disk->path($relativeStoragePath);

            // 3. Load image into GD resource
            $sourceResource = $this->createImageResource($file);
            if (!$sourceResource) {
                Log::error("Failed to create GD image resource for location upload", [
                    'location_name' => $locationName,
                    'original_name' => $file->getClientOriginalName(),
                ]);
                return null;
            }

            // 4. Correct orientation from EXIF if JPEG
            $sourceResource = $this->fixExifOrientation($file, $sourceResource);

            // 5. Dimension safety & aspect ratio preservation
            $processedResource = $this->resizeIfExceedsMax($sourceResource);

            // 6. Convert & write to WebP
            $saved = imagewebp($processedResource, $absoluteDestinationPath, $this->webpQuality);

            // Free GD memory
            if (is_resource($sourceResource) || (is_object($sourceResource) && $sourceResource instanceof \GdImage)) {
                imagedestroy($sourceResource);
            }
            if ($processedResource !== $sourceResource && (is_resource($processedResource) || (is_object($processedResource) && $processedResource instanceof \GdImage))) {
                imagedestroy($processedResource);
            }

            if (!$saved || !file_exists($absoluteDestinationPath)) {
                Log::error("Failed to write WebP image for location", [
                    'destination' => $absoluteDestinationPath,
                    'location_name' => $locationName,
                ]);
                return null;
            }

            return $relativeStoragePath;
        } catch (\Throwable $e) {
            Log::error("Error processing location image upload: " . $e->getMessage(), [
                'location_name' => $locationName,
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Delete an existing image file from storage if present.
     */
    public function deleteImage(?string $path): bool
    {
        if (empty($path) || Str::startsWith($path, ['http://', 'https://'])) {
            return false;
        }

        if (Str::contains($path, '..')) {
            return false;
        }

        $deleted = false;
        $disk = Storage::disk('public');
        if ($disk->exists($path)) {
            $deleted = $disk->delete($path);
        }

        // Also ensure public/storage file removal if directly mapped
        $publicFilePath = public_path('storage/' . ltrim($path, '/'));
        if (file_exists($publicFilePath) && is_file($publicFilePath)) {
            @unlink($publicFilePath);
            $deleted = true;
        }

        return $deleted;
    }

    /**
     * Create GD resource from uploaded file.
     */
    protected function createImageResource(UploadedFile $file)
    {
        $filePath = $file->getRealPath();
        $mime = $file->getMimeType();
        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($mime, ['image/jpeg', 'image/pjpeg', 'image/jpg']) || in_array($ext, ['jpg', 'jpeg'])) {
            return @imagecreatefromjpeg($filePath);
        }

        if ($mime === 'image/png' || $ext === 'png') {
            $img = @imagecreatefrompng($filePath);
            if ($img) {
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
            return $img;
        }

        if ($mime === 'image/webp' || $ext === 'webp') {
            return @imagecreatefromwebp($filePath);
        }

        if ($mime === 'image/gif' || $ext === 'gif') {
            return @imagecreatefromgif($filePath);
        }

        // Fallback using string data
        $content = @file_get_contents($filePath);
        if ($content !== false) {
            return @imagecreatefromstring($content);
        }

        return null;
    }

    /**
     * Auto-rotate image according to camera/phone EXIF orientation.
     */
    protected function fixExifOrientation(UploadedFile $file, $imageResource)
    {
        if (!function_exists('exif_read_data')) {
            return $imageResource;
        }

        try {
            $exif = @exif_read_data($file->getRealPath());
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $rotated = imagerotate($imageResource, 180, 0);
                        imagedestroy($imageResource);
                        return $rotated;
                    case 6:
                        $rotated = imagerotate($imageResource, -90, 0);
                        imagedestroy($imageResource);
                        return $rotated;
                    case 8:
                        $rotated = imagerotate($imageResource, 90, 0);
                        imagedestroy($imageResource);
                        return $rotated;
                }
            }
        } catch (\Throwable $e) {
            // Ignore EXIF read errors
        }

        return $imageResource;
    }

    /**
     * Resize image if either dimension exceeds the maximum threshold, preserving aspect ratio.
     */
    protected function resizeIfExceedsMax($imageResource)
    {
        $width = imagesx($imageResource);
        $height = imagesy($imageResource);

        if ($width <= $this->maxDimension && $height <= $this->maxDimension) {
            return $imageResource;
        }

        $scale = min($this->maxDimension / $width, $this->maxDimension / $height);
        $newWidth = (int) max(1, round($width * $scale));
        $newHeight = (int) max(1, round($height * $scale));

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG / WebP
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled(
            $newImage,
            $imageResource,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        return $newImage;
    }
}
