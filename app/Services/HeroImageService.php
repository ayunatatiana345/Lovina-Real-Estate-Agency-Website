<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroImageService
{
    /**
     * Maximum dimension for hero background image.
     */
    protected int $maxWidth = 2560;
    protected int $maxHeight = 1440;

    /**
     * WebP output compression quality (0-100).
     */
    protected int $webpQuality = 85;

    /**
     * Process an uploaded hero background photo:
     * - Generates an SEO-friendly filename:
     *   north-bali-real-estate-hero-{unique-id}.webp (e.g. north-bali-real-estate-hero-a82f.webp)
     * - Auto-rotates using EXIF orientation if applicable
     * - Scales down if exceeding maximum dimension while strictly preserving aspect ratio
     * - Encodes/converts genuinely to WebP format
     * - Stores in the public cms storage directory (cms/)
     *
     * @param UploadedFile $file
     * @return string|null Relative storage path (e.g. "cms/north-bali-real-estate-hero-a82f.webp") or null on failure
     */
    public function processAndStore(UploadedFile $file): ?string
    {
        try {
            // 1. Generate unique 4-character hex suffix
            $uniqueId = Str::lower(substr(md5(uniqid((string)mt_rand(), true)), 0, 4));
            $filename = "north-bali-real-estate-hero-{$uniqueId}.webp";

            $directory = 'cms';
            $relativeStoragePath = "{$directory}/{$filename}";

            $disk = Storage::disk('public');
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            $absoluteDestinationPath = $disk->path($relativeStoragePath);

            // 2. Load image into GD resource
            $sourceResource = $this->createImageResource($file);
            if (!$sourceResource) {
                Log::error("Failed to create GD image resource for hero upload", [
                    'original_name' => $file->getClientOriginalName(),
                ]);
                return null;
            }

            // 3. Correct orientation from EXIF if JPEG
            $sourceResource = $this->fixExifOrientation($file, $sourceResource);

            // 4. Dimension safety & aspect ratio preservation
            $processedResource = $this->resizeIfExceedsMax($sourceResource);

            // 5. Convert & write genuinely to WebP
            $saved = imagewebp($processedResource, $absoluteDestinationPath, $this->webpQuality);

            // Free GD memory
            if (is_resource($sourceResource) || (is_object($sourceResource) && $sourceResource instanceof \GdImage)) {
                imagedestroy($sourceResource);
            }
            if ($processedResource !== $sourceResource && (is_resource($processedResource) || (is_object($processedResource) && $processedResource instanceof \GdImage))) {
                imagedestroy($processedResource);
            }

            if (!$saved || !file_exists($absoluteDestinationPath)) {
                Log::error("Failed to write WebP hero image", [
                    'destination' => $absoluteDestinationPath,
                ]);
                return null;
            }

            return $relativeStoragePath;
        } catch (\Throwable $e) {
            Log::error("Error processing hero image upload: " . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Delete an existing hero image file from public storage if it was exclusively managed by CMS.
     */
    public function deleteOldHeroImage(?string $path): bool
    {
        if (empty($path) || Str::startsWith($path, ['http://', 'https://'])) {
            return false;
        }

        // Only delete files residing in cms/ directory
        if (!Str::startsWith($path, 'cms/')) {
            return false;
        }

        $disk = Storage::disk('public');
        if ($disk->exists($path)) {
            return $disk->delete($path);
        }

        return false;
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
            // Ignore EXIF errors
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

        if ($width <= $this->maxWidth && $height <= $this->maxHeight) {
            return $imageResource;
        }

        $scale = min($this->maxWidth / $width, $this->maxHeight / $height);
        $newWidth = (int) max(1, round($width * $scale));
        $newHeight = (int) max(1, round($height * $scale));

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for WebP
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
