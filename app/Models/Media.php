<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'mediable_type', 'mediable_id', 'collection_name',
        'file_name', 'file_path', 'disk', 'mime_type',
        'size', 'dimensions', 'custom_properties', 'order_column'
    ];

    protected $casts = [
        'dimensions' => 'array',
        'custom_properties' => 'array',
        'size' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mediable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeInCollection($query, $collectionName)
    {
        return $query->where('collection_name', $collectionName);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_column');
    }

    // Methods
    public function getFullUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }

    public function getHumanSizeAttribute()
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isVideo()
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    public function isDocument()
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }

    public function delete()
    {
        // Delete the file from storage
        if (Storage::disk($this->disk)->exists($this->file_path)) {
            Storage::disk($this->disk)->delete($this->file_path);
        }

        // Delete the database record
        return parent::delete();
    }
}
