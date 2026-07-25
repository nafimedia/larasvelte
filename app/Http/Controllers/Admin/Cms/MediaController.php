<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaFile::latest();

        if ($request->filled('search')) {
            $query->where('original_name', 'like', '%' . $request->search . '%')
                  ->orWhere('alt_text', 'like', '%' . $request->search . '%');
        }

        $media = $query->paginate(24)->withQueryString();

        return Inertia::render('Admin/Cms/Media/Index', [
            'mediaFiles' => $media,
            'filters' => $request->only('search'),
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // max 10MB
            'alt_text' => 'nullable|string',
            'caption' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/cms', $filename, 'public');

        $media = MediaFile::create([
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => Storage::url($path),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'alt_text' => $request->input('alt_text'),
            'caption' => $request->input('caption'),
        ]);

        return redirect()->back()->with('success', 'File media berhasil diunggah');
    }

    public function destroy($id)
    {
        $media = MediaFile::findOrFail($id);
        $media->delete();

        return redirect()->back()->with('success', 'File media berhasil dihapus');
    }
}
