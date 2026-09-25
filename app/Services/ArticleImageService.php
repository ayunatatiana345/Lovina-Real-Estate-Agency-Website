<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleImageService
{
    /**
     * Maximum dimension for article hero/featured images.
     */
    protected int $maxDimension = 2560;

    /**
     * WebP output compression quality (0-100).
     */
    protected int $webpQuality = 85;

    /**
     * Process an uploaded article hero/featured photo:
     * - Generates an SEO-friendly filename from article title or slug:
     *   e.g. "How to Choose the Right Property in Bali" -> "how-to-choose-the-right-property-in-bali-a82f.webp"
     * - Auto-rotates using EXIF orientation if applicable
     * - Scales down if exceeding maximum dimension while strictly preserving aspect ratio
     * - Encodes/converts genuinely to WebP format
     * - Stores in the public articles storage directory (articles/)
     *
     * @param UploadedFile $file
     * @param string $titleOrSlug
     * @return string|null Relative storage path (e.g. "articles/how-to-choose-the-right-property-in-bali-a82f.webp") or null on failure
     */
    public function processAndStore(UploadedFile $file, string $titleOrSlug): ?string
    {
        try {
            // 1. Generate clean, lowercase, hyphenated SEO-friendly base slug
            $baseSlug = Str::slug($titleOrSlug);
            if (empty($baseSlug)) {
                $baseSlug = 'article';
            }

            // 2. Generate unique 4-character hex suffix to prevent collisions
            $uniqueId = Str::lower(substr(md5(uniqid((string)mt_rand(), true)), 0, 4));
            $filename = "{$baseSlug}-{$uniqueId}.webp";

            $directory = 'articles';
            $relativeStoragePath = "{$directory}/{$filename}";

            $disk = Storage::disk('public');
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            $absoluteDestinationPath = $disk->path($relativeStoragePath);

            // 3. Load image into GD resource
            $sourceResource = $this->createImageResource($file);
            if (!$sourceResource) {
                Log::error("Failed to create GD image resource for article upload", [
                    'original_name' => $file->getClientOriginalName(),
                    'title' => $titleOrSlug,
                ]);
                return null;
            }

            // 4. Correct orientation from EXIF if JPEG
            $sourceResource = $this->fixExifOrientation($file, $sourceResource);

            // 5. Dimension safety & aspect ratio preservation
            $processedResource = $this->resizeIfExceedsMax($sourceResource);

            // 6. Convert & write genuinely to WebP
            $saved = imagewebp($processedResource, $absoluteDestinationPath, $this->webpQuality);

            // Free GD memory
            if (is_resource($sourceResource) || (is_object($sourceResource) && $sourceResource instanceof \GdImage)) {
                imagedestroy($sourceResource);
            }
            if ($processedResource !== $sourceResource && (is_resource($processedResource) || (is_object($processedResource) && $processedResource instanceof \GdImage))) {
                imagedestroy($processedResource);
            }

            if (!$saved || !file_exists($absoluteDestinationPath)) {
                Log::error("Failed to write WebP article image", [
                    'destination' => $absoluteDestinationPath,
                    'title' => $titleOrSlug,
                ]);
                return null;
            }

            return $relativeStoragePath;
        } catch (\Throwable $e) {
            Log::error("Error processing article image upload: " . $e->getMessage(), [
                'title' => $titleOrSlug,
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Process an uploaded article body/content photo:
     * - Generates an SEO-friendly filename from article title or slug with content marker and sequence:
     *   e.g. "How to Choose the Right Property in Bali" -> "how-to-choose-the-right-property-in-bali-content-01-a82f.webp"
     * - Auto-rotates using EXIF orientation if applicable
     * - Scales down if exceeding maximum dimension while strictly preserving aspect ratio
     * - Encodes/converts genuinely to WebP format
     * - Stores in the public articles storage directory (articles/)
     *
     * @param UploadedFile $file
     * @param string $titleOrSlug
     * @return string|null Relative storage path (e.g. "articles/how-to-choose-the-right-property-in-bali-content-01-a82f.webp") or null on failure
     */
    public function processAndStoreContentImage(UploadedFile $file, string $titleOrSlug): ?string
    {
        try {
            // 1. Generate clean, lowercase, hyphenated SEO-friendly base slug
            $baseSlug = Str::slug($titleOrSlug);
            if (empty($baseSlug)) {
                $baseSlug = 'article';
            }

            $disk = Storage::disk('public');
            $directory = 'articles';
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            // Find next sequence number for this article's content images
            $existingFiles = $disk->files($directory);
            $seqCount = 1;
            foreach ($existingFiles as $existingFile) {
                if (str_contains(basename($existingFile), "{$baseSlug}-content-")) {
                    $seqCount++;
                }
            }
            $formattedSeq = str_pad((string)$seqCount, 2, '0', STR_PAD_LEFT);

            // 2. Generate unique 4-character hex suffix to prevent collisions
            $uniqueId = Str::lower(substr(md5(uniqid((string)mt_rand(), true)), 0, 4));
            $filename = "{$baseSlug}-content-{$formattedSeq}-{$uniqueId}.webp";

            $relativeStoragePath = "{$directory}/{$filename}";
            $absoluteDestinationPath = $disk->path($relativeStoragePath);

            // 3. Load image into GD resource
            $sourceResource = $this->createImageResource($file);
            if (!$sourceResource) {
                Log::error("Failed to create GD image resource for article content upload", [
                    'original_name' => $file->getClientOriginalName(),
                    'title' => $titleOrSlug,
                ]);
                return null;
            }

            // 4. Correct orientation from EXIF if JPEG
            $sourceResource = $this->fixExifOrientation($file, $sourceResource);

            // 5. Dimension safety & aspect ratio preservation
            $processedResource = $this->resizeIfExceedsMax($sourceResource);

            // 6. Convert & write genuinely to WebP
            $saved = imagewebp($processedResource, $absoluteDestinationPath, $this->webpQuality);

            // Free GD memory
            if (is_resource($sourceResource) || (is_object($sourceResource) && $sourceResource instanceof \GdImage)) {
                imagedestroy($sourceResource);
            }
            if ($processedResource !== $sourceResource && (is_resource($processedResource) || (is_object($processedResource) && $processedResource instanceof \GdImage))) {
                imagedestroy($processedResource);
            }

            if (!$saved || !file_exists($absoluteDestinationPath)) {
                Log::error("Failed to write WebP article content image", [
                    'destination' => $absoluteDestinationPath,
                    'title' => $titleOrSlug,
                ]);
                return null;
            }

            return $relativeStoragePath;
        } catch (\Throwable $e) {
            Log::error("Error processing article content image upload: " . $e->getMessage(), [
                'title' => $titleOrSlug,
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Delete an existing article image file from public storage if present.
     */
    public function deleteOldImage(?string $path): bool
    {
        if (empty($path) || Str::startsWith($path, ['http://', 'https://', 'images/'])) {
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

        // Preserve transparency
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
