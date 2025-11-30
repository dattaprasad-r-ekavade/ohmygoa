<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user's media library
     */
    public function index(Request $request)
    {
        $query = Auth::user()->media();

        // Filter by collection
        if ($request->filled('collection')) {
            $query->inCollection($request->collection);
        }

        // Filter by type
        if ($request->filled('type')) {
            $mimeType = match($request->type) {
                'image' => 'image/%',
                'video' => 'video/%',
                'document' => 'application/%',
                default => '%'
            };
            $query->where('mime_type', 'like', $mimeType);
        }

        // Search by filename
        if ($request->filled('search')) {
            $query->where('file_name', 'like', '%' . $request->search . '%');
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $media = $query->paginate(24);

        // Statistics
        $stats = [
            'total_files' => Auth::user()->media()->count(),
            'total_size' => Auth::user()->media()->sum('size'),
            'images' => Auth::user()->media()->where('mime_type', 'like', 'image/%')->count(),
            'videos' => Auth::user()->media()->where('mime_type', 'like', 'video/%')->count(),
            'documents' => Auth::user()->media()->where('mime_type', 'like', 'application/%')->count()
        ];

        return view('media.index', compact('media', 'stats'));
    }

    /**
     * Upload media file
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'collection' => 'nullable|string|max:100',
            'mediable_type' => 'nullable|string',
            'mediable_id' => 'nullable|integer'
        ]);

        $file = $request->file('file');

        // Validate file type
        $allowedMimeTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'video/mp4', 'video/mpeg', 'video/webm',
            'application/pdf', 'application/msword', 'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            return back()->with('error', 'File type not allowed');
        }

        // Store file
        $path = $file->store('media/' . date('Y/m'), 'public');
        
        // Get image dimensions if it's an image
        $dimensions = null;
        if (str_starts_with($file->getMimeType(), 'image/')) {
            [$width, $height] = getimagesize($file->getRealPath());
            $dimensions = ['width' => $width, 'height' => $height];
        }

        // Create media record
        $media = Auth::user()->media()->create([
            'mediable_type' => $request->mediable_type,
            'mediable_id' => $request->mediable_id,
            'collection_name' => $request->collection ?? 'default',
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'disk' => 'public',
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'dimensions' => $dimensions,
            'order_column' => Auth::user()->media()->max('order_column') + 1
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'media' => $media,
                'url' => $media->full_url
            ]);
        }

        return back()->with('success', 'File uploaded successfully');
    }

    /**
     * Bulk upload media files
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'file|max:10240',
            'collection' => 'nullable|string|max:100'
        ]);

        $uploaded = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $path = $file->store('media/' . date('Y/m'), 'public');
                
                $dimensions = null;
                if (str_starts_with($file->getMimeType(), 'image/')) {
                    [$width, $height] = getimagesize($file->getRealPath());
                    $dimensions = ['width' => $width, 'height' => $height];
                }

                $media = Auth::user()->media()->create([
                    'collection_name' => $request->collection ?? 'default',
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'disk' => 'public',
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'dimensions' => $dimensions,
                    'order_column' => Auth::user()->media()->max('order_column') + 1
                ]);

                $uploaded[] = $media;

            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        $message = count($uploaded) . ' files uploaded successfully';
        if (count($errors) > 0) {
            $message .= '. ' . count($errors) . ' files failed.';
        }

        return back()->with('success', $message);
    }

    /**
     * Show media details
     */
    public function show(Media $media)
    {
        if ($media->user_id !== Auth::id()) {
            abort(403);
        }

        return view('media.show', compact('media'));
    }

    /**
     * Update media details
     */
    public function update(Request $request, Media $media)
    {
        if ($media->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'file_name' => 'nullable|string|max:255',
            'collection_name' => 'nullable|string|max:100',
            'custom_properties' => 'nullable|array'
        ]);

        $media->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'media' => $media]);
        }

        return back()->with('success', 'Media updated successfully');
    }

    /**
     * Delete media
     */
    public function destroy(Media $media)
    {
        if ($media->user_id !== Auth::id()) {
            abort(403);
        }

        $media->delete(); // Will also delete the physical file

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Media deleted successfully');
    }

    /**
     * Bulk delete media
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:media,id'
        ]);

        $media = Media::whereIn('id', $request->media_ids)
            ->where('user_id', Auth::id())
            ->get();

        foreach ($media as $item) {
            $item->delete();
        }

        return back()->with('success', count($media) . ' files deleted successfully');
    }

    /**
     * Update media order
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:media,id'
        ]);

        foreach ($request->order as $index => $mediaId) {
            Media::where('id', $mediaId)
                ->where('user_id', Auth::id())
                ->update(['order_column' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Move media to collection
     */
    public function moveToCollection(Request $request)
    {
        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:media,id',
            'collection' => 'required|string|max:100'
        ]);

        Media::whereIn('id', $request->media_ids)
            ->where('user_id', Auth::id())
            ->update(['collection_name' => $request->collection]);

        return back()->with('success', 'Media moved to ' . $request->collection);
    }

    /**
     * Get media for selection (AJAX)
     */
    public function select(Request $request)
    {
        $query = Auth::user()->media();

        if ($request->filled('collection')) {
            $query->inCollection($request->collection);
        }

        if ($request->filled('type')) {
            $mimeType = match($request->type) {
                'image' => 'image/%',
                'video' => 'video/%',
                'document' => 'application/%',
                default => '%'
            };
            $query->where('mime_type', 'like', $mimeType);
        }

        $media = $query->ordered()->paginate(20);

        return response()->json([
            'media' => $media->items(),
            'pagination' => [
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'total' => $media->total()
            ]
        ]);
    }

    /**
     * Download media file
     */
    public function download(Media $media)
    {
        if ($media->user_id !== Auth::id()) {
            abort(403);
        }

        return Storage::disk($media->disk)->download($media->file_path, $media->file_name);
    }
}
