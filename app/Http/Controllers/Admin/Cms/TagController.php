<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::withCount('posts');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('slug', 'like', "%{$request->search}%");
        }

        $tags = $query->latest('id')->paginate($request->input('per_page', 10))->withQueryString();

        return Inertia::render('Admin/Cms/Tags/Index', [
            'tags' => $tags,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags,slug',
            'color' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        Tag::create($validated);

        return redirect()->back()->with('success', 'Tag baru berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tags,slug,' . $id,
            'color' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);

        $tag->update($validated);

        return redirect()->back()->with('success', 'Tag berhasil diperbarui');
    }

    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return redirect()->back()->with('success', 'Tag berhasil dihapus');
    }
}
