<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    /**
     * Upload file to storage.
     */
    public function upload(UploadedFile $file, string $directory = 'uploads', string $disk = 'public'): string
    {
        $filename = $this->generateFilename($file);
        $path = $file->storeAs($directory, $filename, $disk);

        return $path;
    }

    /**
     * Upload multiple files.
     */
    public function uploadMultiple(array $files, string $directory = 'uploads', string $disk = 'public'): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->upload($file, $directory, $disk);
            }
        }

        return $paths;
    }

    /**
     * Upload and resize image.
     */
    public function uploadImage(UploadedFile $file, string $directory = 'images', int $maxWidth = 1920, int $maxHeight = 1080): string
    {
        $filename = $this->generateFilename($file);
        $path = storage_path("app/public/{$directory}/{$filename}");

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // Resize image
        $image = Image::make($file);
        
        if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
            $image->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $image->save($path, 85);

        return "{$directory}/{$filename}";
    }

    /**
     * Upload and create thumbnail.
     */
    public function uploadWithThumbnail(UploadedFile $file, string $directory = 'images', int $thumbWidth = 300, int $thumbHeight = 300): array
    {
        $filename = $this->generateFilename($file);
        $mainPath = storage_path("app/public/{$directory}/{$filename}");
        $thumbPath = storage_path("app/public/{$directory}/thumbs/{$filename}");

        // Ensure directories exist
        if (!file_exists(dirname($mainPath))) {
            mkdir(dirname($mainPath), 0755, true);
        }
        if (!file_exists(dirname($thumbPath))) {
            mkdir(dirname($thumbPath), 0755, true);
        }

        // Save main image
        $image = Image::make($file);
        $image->resize(1920, 1080, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($mainPath, 85);

        // Create thumbnail
        $thumbnail = Image::make($file);
        $thumbnail->fit($thumbWidth, $thumbHeight)->save($thumbPath, 85);

        return [
            'main' => "{$directory}/{$filename}",
            'thumbnail' => "{$directory}/thumbs/{$filename}",
        ];
    }

    /**
     * Delete file from storage.
     */
    public function delete(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Delete multiple files.
     */
    public function deleteMultiple(array $paths, string $disk = 'public'): void
    {
        foreach ($paths as $path) {
            $this->delete($path, $disk);
        }
    }

    /**
     * Generate unique filename.
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $timestamp = time();
        $random = Str::random(8);

        return "{$basename}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Validate file type.
     */
    public function validateFileType(UploadedFile $file, array $allowedTypes): bool
    {
        return in_array($file->getMimeType(), $allowedTypes);
    }

    /**
     * Validate file size.
     */
    public function validateFileSize(UploadedFile $file, int $maxSizeKB): bool
    {
        return $file->getSize() <= ($maxSizeKB * 1024);
    }

    /**
     * Get file size in human readable format.
     */
    public function getFileSize(string $path, string $disk = 'public'): string
    {
        if (!Storage::disk($disk)->exists($path)) {
            return '0 B';
        }

        $bytes = Storage::disk($disk)->size($path);
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;

        return number_format($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }
}
