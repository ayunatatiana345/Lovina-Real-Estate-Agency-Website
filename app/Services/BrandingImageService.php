<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandingImageService
{
    /**
     * WebP output compression quality (0-100).
     */
    protected int $webpQuality = 90;

    /**
     * Maximum dimension for branding logo images.
     */
    protected int $maxDimension = 2000;

    /**
     * Process and store a primary logo upload.
     * Example output filename: lovina-north-bali-real-estate-logo-a82f.webp
     */
    public function processAndStorePrimaryLogo(UploadedFile $file): ?string
    {
        return $this->processAndStore(
            $file,
            'lovina-north-bali-real-estate-logo'
        );
    }

    /**
     * Process and store an alternative logo upload.
     * Example output filename: lovina-north-bali-real-estate-alternative-logo-a82f.webp
     */
    public function processAndStoreAltLogo(UploadedFile $file): ?string
    {
        return $this->processAndStore(
            $file,
            'lovina-north-bali-real-estate-alternative-logo'
        );
    }

    /**
     * Process and store a site icon / favicon upload.
     * Example output filename: lovina-north-bali-real-estate-favicon-a82f.webp (or .ico)
     */
    public function processAndStoreFavicon(UploadedFile $file): ?string
    {
        return $this->processAndStore(
            $file,
            'lovina-north-bali-real-estate-favicon',
            512
        );
    }

    /**
     * Process and store a social share image upload.
     * Example output filename: lovina-north-bali-real-estate-social-share-a82f.webp
     */
    public function processAndStoreSocialImage(UploadedFile $file): ?string
    {
        return $this->processAndStore(
            $file,
            'lovina-north-bali-real-estate-social-share',
            1920
        );
    }

    /**
     * Core processing method for branding images.
     */
    public function processAndStore(UploadedFile $file, string $baseSlug, ?int $maxDim = null): ?string
    {
        try {
            $uniqueId = Str::lower(substr(md5(uniqid((string)mt_rand(), true)), 0, 4));
            $ext = strtolower($file->getClientOriginalExtension());
            $mime = $file->getMimeType();

            $directory = 'branding';
            $disk = Storage::disk('public');
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            // Handle Vector SVG or ICO direct binary store without rasterization
            if ($ext === 'svg' || str_contains($mime, 'svg')) {
                $filename = "{$baseSlug}-{$uniqueId}.svg";
                $relativeStoragePath = "{$directory}/{$filename}";
                $disk->put($relativeStoragePath, file_get_contents($file->getRealPath()));
                return $relativeStoragePath;
            }

            if ($ext === 'ico' || str_contains($mime, 'vnd.microsoft.icon') || str_contains($mime, 'x-icon')) {
                $filename = "{$baseSlug}-{$uniqueId}.ico";
                $relativeStoragePath = "{$directory}/{$filename}";
                $disk->put($relativeStoragePath, file_get_contents($file->getRealPath()));
                return $relativeStoragePath;
            }

            // All raster images: Convert genuinely to WebP format
            $filename = "{$baseSlug}-{$uniqueId}.webp";
            $relativeStoragePath = "{$directory}/{$filename}";
            $absoluteDestinationPath = $disk->path($relativeStoragePath);

            $sourceResource = $this->createImageResource($file);
            if (!$sourceResource) {
                // If GD fails to decode, fallback to direct store preserving extension
                Log::warning("GD could not decode branding image, storing raw: " . $file->getClientOriginalName());
                $rawFilename = "{$baseSlug}-{$uniqueId}." . ($ext ?: 'webp');
                $rawPath = "{$directory}/{$rawFilename}";
                $disk->put($rawPath, file_get_contents($file->getRealPath()));
                return $rawPath;
            }

            // Dimension check & aspect ratio preservation
            $targetMax = $maxDim ?? $this->maxDimension;
            $processedResource = $this->resizeIfExceedsMax($sourceResource, $targetMax);

            // Genuine WebP conversion with alpha transparency preservation
            $saved = imagewebp($processedResource, $absoluteDestinationPath, $this->webpQuality);

            if (is_resource($sourceResource) || (is_object($sourceResource) && $sourceResource instanceof \GdImage)) {
                imagedestroy($sourceResource);
            }
            if ($processedResource !== $sourceResource && (is_resource($processedResource) || (is_object($processedResource) && $processedResource instanceof \GdImage))) {
                imagedestroy($processedResource);
            }

            if (!$saved || !file_exists($absoluteDestinationPath)) {
                Log::error("Failed to write WebP branding image to destination: " . $absoluteDestinationPath);
                return null;
            }

            return $relativeStoragePath;
        } catch (\Throwable $e) {
            Log::error("Error processing branding image: " . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Delete old uploaded branding file from storage if safe.
     */
    public function deleteOldBrandingImage(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        // Never delete core static assets in public/images/ or root favicon.ico
        if (Str::startsWith($path, ['http://', 'https://', 'images/', 'img/']) || $path === 'favicon.ico') {
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
        if (!$filePath || !file_exists($filePath)) {
            return null;
        }

        $content = @file_get_contents($filePath);
        if ($content !== false && $content !== '') {
            $img = @imagecreatefromstring($content);
            if ($img) {
                imagealphablending($img, true);
                imagesavealpha($img, true);
                return $img;
            }
        }

        $mime = $file->getMimeType();
        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($mime, ['image/jpeg', 'image/pjpeg', 'image/jpg']) || in_array($ext, ['jpg', 'jpeg'])) {
            $img = @imagecreatefromjpeg($filePath);
            if ($img) return $img;
        }

        if ($mime === 'image/png' || $ext === 'png') {
            $img = @imagecreatefrompng($filePath);
            if ($img) {
                imagealphablending($img, true);
                imagesavealpha($img, true);
                return $img;
            }
        }

        if ($mime === 'image/webp' || $ext === 'webp') {
            $img = @imagecreatefromwebp($filePath);
            if ($img) return $img;
        }

        if ($mime === 'image/gif' || $ext === 'gif') {
            $img = @imagecreatefromgif($filePath);
            if ($img) return $img;
        }

        return null;
    }

    /**
     * Resize image if either dimension exceeds the maximum threshold, preserving aspect ratio and alpha transparency.
     */
    protected function resizeIfExceedsMax($imageResource, int $maxDimension)
    {
        $width = imagesx($imageResource);
        $height = imagesy($imageResource);

        if ($width <= $maxDimension && $height <= $maxDimension) {
            return $imageResource;
        }

        $scale = min($maxDimension / $width, $maxDimension / $height);
        $newWidth = (int) max(1, round($width * $scale));
        $newHeight = (int) max(1, round($height * $scale));

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve alpha transparency
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
