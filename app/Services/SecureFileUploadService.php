<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class SecureFileUploadService
{
    /**
     * Allowed image MIME types.
     */
    protected array $allowedImageTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Allowed document MIME types.
     */
    protected array $allowedDocumentTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    /**
     * Max file sizes in KB.
     */
    protected array $maxFileSizes = [
        'image' => 5120, // 5MB
        'document' => 10240, // 10MB
        'video' => 51200, // 50MB
    ];

    /**
     * Upload and secure an image file.
     */
    public function uploadImage(UploadedFile $file, string $directory = 'images', bool $optimize = true): array
    {
        $this->validateImage($file);

        // Generate secure filename
        $filename = $this->generateSecureFilename($file);
        $path = $directory . '/' . date('Y/m');

        // Optimize image if needed
        if ($optimize) {
            $optimizedFile = $this->optimizeImage($file);
            $storedPath = Storage::disk('public')->putFileAs($path, $optimizedFile, $filename);
        } else {
            $storedPath = $file->storeAs($path, $filename, 'public');
        }

        return [
            'path' => $storedPath,
            'url' => Storage::disk('public')->url($storedPath),
            'filename' => $filename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    /**
     * Upload and secure a document file.
     */
    public function uploadDocument(UploadedFile $file, string $directory = 'documents'): array
    {
        $this->validateDocument($file);

        $filename = $this->generateSecureFilename($file);
        $path = $directory . '/' . date('Y/m');
        
        $storedPath = $file->storeAs($path, $filename, 'public');

        return [
            'path' => $storedPath,
            'url' => Storage::disk('public')->url($storedPath),
            'filename' => $filename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    /**
     * Validate image file.
     */
    protected function validateImage(UploadedFile $file): void
    {
        // Check MIME type
        if (!in_array($file->getMimeType(), $this->allowedImageTypes)) {
            throw new \InvalidArgumentException('Invalid image type. Allowed types: jpg, jpeg, png, gif, webp');
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSizes['image'] * 1024) {
            throw new \InvalidArgumentException('Image file too large. Maximum size: ' . ($this->maxFileSizes['image'] / 1024) . 'MB');
        }

        // Validate it's actually an image
        $imageInfo = @getimagesize($file->getRealPath());
        if ($imageInfo === false) {
            throw new \InvalidArgumentException('Invalid image file');
        }
    }

    /**
     * Validate document file.
     */
    protected function validateDocument(UploadedFile $file): void
    {
        if (!in_array($file->getMimeType(), $this->allowedDocumentTypes)) {
            throw new \InvalidArgumentException('Invalid document type. Allowed types: pdf, doc, docx, xls, xlsx');
        }

        if ($file->getSize() > $this->maxFileSizes['document'] * 1024) {
            throw new \InvalidArgumentException('Document file too large. Maximum size: ' . ($this->maxFileSizes['document'] / 1024) . 'MB');
        }
    }

    /**
     * Generate secure filename.
     */
    protected function generateSecureFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // Sanitize basename
        $basename = Str::slug($basename);
        $basename = substr($basename, 0, 50);
        
        // Generate unique filename
        return $basename . '_' . time() . '_' . Str::random(8) . '.' . $extension;
    }

    /**
     * Optimize image (resize, compress).
     */
    protected function optimizeImage(UploadedFile $file): UploadedFile
    {
        // Note: This requires intervention/image package
        // For production, implement actual optimization
        // For now, return original file
        return $file;
    }

    /**
     * Delete file from storage.
     */
    public function deleteFile(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Check if file exists.
     */
    public function fileExists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }
}
