<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadService
{
    /**
     * Upload a file to storage with agency-scoped path.
     */
    public function upload(
        UploadedFile $file,
        int $agencyId,
        string $directory = 'content',
        ?string $disk = null
    ): array {
        $disk = $disk ?? config('filesystems.default');
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = "agencies/{$agencyId}/{$directory}/{$filename}";

        $storedPath = $file->storeAs("agencies/{$agencyId}/{$directory}", $filename, $disk);

        return [
            'path' => $storedPath,
            'url' => Storage::disk($disk)->url($storedPath),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
        ];
    }

    /**
     * Delete a file from storage.
     */
    public function delete(string $path, ?string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default');
        return Storage::disk($disk)->delete($path);
    }

    /**
     * Get supported MIME types for social platforms.
     */
    public function getSupportedMimeTypes(string $platform): array
    {
        return match ($platform) {
            'facebook' => ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/quicktime'],
            'instagram' => ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/quicktime'],
            'twitter' => ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'],
            'linkedin' => ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'],
            'tiktok' => ['video/mp4', 'video/quicktime'],
            'pinterest' => ['image/jpeg', 'image/png', 'image/gif'],
            default => ['image/jpeg', 'image/png'],
        };
    }

    /**
     * Validate file size for platform.
     */
    public function getMaxFileSize(string $platform): int
    {
        return match ($platform) {
            'facebook' => 10 * 1024 * 1024, // 10MB
            'instagram' => 8 * 1024 * 1024, // 8MB
            'twitter' => 5 * 1024 * 1024, // 5MB
            'linkedin' => 10 * 1024 * 1024, // 10MB
            'tiktok' => 50 * 1024 * 1024, // 50MB
            'pinterest' => 10 * 1024 * 1024, // 10MB
            default => 10 * 1024 * 1024,
        };
    }
}
