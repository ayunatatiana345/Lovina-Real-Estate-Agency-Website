<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyImageService
{
    /**
     * Maximum width and height for property gallery photos to preserve web performance.
     */
    protected int $maxDimension = 2048;

    /**
     * WebP output compression quality (0-100).
     */
    protected int $webpQuality = 82;

    /**
     * Process an uploaded property photo:
     * - Generates an SEO-friendly deterministic filename
     * - Auto-rotates using EXIF orientation if applicable
     * - Scales down large phone/camera images preserving aspect ratio
     * - Converts to WebP format
     * - Stores in the public property storage directory
     * - Generates meaningful SEO alt text
     *
     * @param UploadedFile $file
     * @param Property $property
     * @param int $sequence
     * @return array|null Returns ['path' => string, 'alt' => string, 'filename' => string] or null on failure
     */
    public function processAndStore(UploadedFile $file, Property $property, int $sequence = 1): ?array
    {
        try {
            // 1. Generate clean property slug
            $rawSlug = $property->slug ?: Str::slug($property->name);
            $slug = Str::slug($rawSlug) ?: 'property';

            // 2. Generate SEO-friendly filename: {property-slug}-{seq}-{unique}.webp
            $formattedSeq = str_pad((string)$sequence, 2, '0', STR_PAD_LEFT);
            $uniqueId = substr(bin2hex(random_bytes(4)), 0, 4);
            $filename = "{$slug}-{$formattedSeq}-{$uniqueId}.webp";

            // Relative directory in public storage
            $directory = "properties/{$slug}";
            $relativeStoragePath = "{$directory}/{$filename}";

            // Ensure destination directory exists on public disk
            $disk = Storage::disk('public');
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            $absoluteDestinationPath = $disk->path($relativeStoragePath);

            // 3. Load image into GD resource
            $sourceResource = $this->createImageResource($file);
            if (!$sourceResource) {
                Log::error("Failed to create GD image resource for uploaded file", [
                    'property_id' => $property->id,
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
                Log::error("Failed to write WebP image to destination", [
                    'destination' => $absoluteDestinationPath,
                    'property_id' => $property->id,
                ]);
                return null;
            }

            // 7. Generate SEO-friendly Alt Text based on actual property details
            $altText = $this->generateAltText($property, $sequence);

            return [
                'path' => $relativeStoragePath,
                'alt' => $altText,
                'filename' => $filename,
            ];
        } catch (\Throwable $e) {
            Log::error("Error processing property image upload: " . $e->getMessage(), [
                'property_id' => $property->id ?? null,
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
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
            // Ignore EXIF read errors on files without EXIF headers
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

    /**
     * Generate an SEO-rich, descriptive alt text strictly using actual property data.
     */
    protected function generateAltText(Property $property, int $sequence): string
    {
        $propName = trim($property->name);
        $locationName = $property->location->name ?? 'North Bali';

        // Base: "[Property Name] in [Location], North Bali"
        if (!Str::contains(strtolower($propName), strtolower($locationName))) {
            $baseAlt = "{$propName} in {$locationName}, North Bali";
        } else {
            $baseAlt = Str::contains(strtolower($propName), 'bali') ? $propName : "{$propName}, North Bali";
        }

        // For multiple photos, append photo sequence indicator
        if ($sequence > 1) {
            return "{$baseAlt} - Photo {$sequence}";
        }

        return $baseAlt;
    }
}
