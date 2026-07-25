<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::with(['author', 'parent']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'trash') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        $pages = $query->orderBy('order', 'asc')
            ->latest('id')
            ->paginate($request->input('per_page', 10))
            ->withQueryString();

        $allPages = Page::select('id', 'title', 'parent_id')->get();

        return Inertia::render('Admin/Cms/Pages/Index', [
            'pages' => $pages,
            'allPages' => $allPages,
            'filters' => $request->only(['search', 'status', 'per_page']),
        ]);
    }

    public function create()
    {
        $allPages = Page::select('id', 'title')->get();

        return Inertia::render('Admin/Cms/Pages/Edit', [
            'pageItem' => null,
            'allPages' => $allPages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'banner_image' => 'nullable|string',
            'parent_id' => 'nullable|exists:pages,id',
            'template' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'required|in:draft,published,scheduled,private,archived,trash',
            'visibility' => 'required|in:public,private,password',
            'password' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'canonical_url' => 'nullable|string',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
        ]);

        $slug = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']);

        Page::create(array_merge($validated, [
            'slug' => $slug,
            'author_id' => auth()->id(),
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]));

        return redirect()->route('admin.cms.pages.index')->with('success', 'Halaman baru berhasil dibuat');
    }

    public function edit($id)
    {
        $pageItem = Page::withTrashed()->findOrFail($id);
        $allPages = Page::where('id', '!=', $id)->select('id', 'title')->get();

        return Inertia::render('Admin/Cms/Pages/Edit', [
            'pageItem' => $pageItem,
            'allPages' => $allPages,
        ]);
    }

    public function update(Request $request, $id)
    {
        $pageItem = Page::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $id,
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'banner_image' => 'nullable|string',
            'parent_id' => 'nullable|exists:pages,id',
            'template' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'required|in:draft,published,scheduled,private,archived,trash',
            'visibility' => 'required|in:public,private,password',
            'password' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'canonical_url' => 'nullable|string',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        if ($validated['status'] === 'published' && !$pageItem->published_at) {
            $validated['published_at'] = now();
        }

        $pageItem->update($validated);

        return redirect()->route('admin.cms.pages.index')->with('success', 'Halaman berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pageItem = Page::findOrFail($id);
        $pageItem->delete();

        return redirect()->back()->with('success', 'Halaman berhasil dipindahkan ke tempat sampah');
    }

    public function restore($id)
    {
        $pageItem = Page::onlyTrashed()->findOrFail($id);
        $pageItem->restore();

        return redirect()->back()->with('success', 'Halaman berhasil dipulihkan');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'action' => 'required|in:publish,draft,delete,restore',
        ]);

        $action = $request->input('action');
        $ids = $request->input('ids');

        if ($action === 'delete') {
            Page::whereIn('id', $ids)->delete();
        } elseif ($action === 'restore') {
            Page::onlyTrashed()->whereIn('id', $ids)->restore();
        } else {
            Page::whereIn('id', $ids)->update(['status' => $action]);
        }

        return redirect()->back()->with('success', 'Tindakan massal berhasil diterapkan');
    }
}
