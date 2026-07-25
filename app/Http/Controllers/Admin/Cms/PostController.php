<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['author', 'category', 'tags']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'trash') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        $posts = $query->latest('id')
            ->paginate($request->input('per_page', 10))
            ->withQueryString();

        $categories = Category::all();
        $tags = Tag::all();

        return Inertia::render('Admin/Cms/Posts/Index', [
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags,
            'filters' => $request->only(['search', 'category', 'status', 'per_page']),
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return Inertia::render('Admin/Cms/Posts/Edit', [
            'postItem' => null,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'nullable|array',
            'is_sticky' => 'boolean',
            'is_featured' => 'boolean',
            'allow_comment' => 'boolean',
            'status' => 'required|in:draft,published,scheduled,private,archived,trash',
            'published_at' => 'nullable|string',
            'timezone' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'canonical_url' => 'nullable|string',
        ]);

        $slug = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $wordCount = str_word_count(strip_tags($validated['content'] ?? ''));
        $readingTime = max(1, (int) ceil($wordCount / 200));

        $publishedAt = null;
        if (!empty($validated['published_at'])) {
            $publishedAt = \Carbon\Carbon::parse($validated['published_at']);
        } elseif ($validated['status'] === 'published') {
            $publishedAt = now();
        }

        $post = Post::create(array_merge($validated, [
            'slug' => $slug,
            'author_id' => auth()->id(),
            'reading_time' => $readingTime,
            'published_at' => $publishedAt,
        ]));

        if (!empty($validated['tag_ids'])) {
            $post->tags()->sync($validated['tag_ids']);
        }

        // Create initial revision
        \App\Models\PostRevision::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'title' => $post->title,
            'summary' => $post->summary,
            'content' => $post->content,
        ]);

        return redirect()->route('admin.cms.posts.index')->with('success', 'Artikel berhasil disimpan');
    }

    public function edit($id)
    {
        $postItem = Post::withTrashed()->with(['tags', 'category', 'revisions.user'])->findOrFail($id);
        $postItem->ensurePreviewToken();
        $categories = Category::all();
        $tags = Tag::all();
        $allPosts = Post::where('id', '!=', $id)->select('id', 'title', 'slug')->get();

        return Inertia::render('Admin/Cms/Posts/Edit', [
            'postItem' => $postItem,
            'categories' => $categories,
            'tags' => $tags,
            'allPosts' => $allPosts,
            'revisions' => $postItem->revisions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $id,
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'nullable|array',
            'manual_related_ids' => 'nullable|array',
            'is_sticky' => 'boolean',
            'is_featured' => 'boolean',
            'allow_comment' => 'boolean',
            'status' => 'required|in:draft,review,revision,approved,published,scheduled,private,archived,trash',
            'published_at' => 'nullable|string',
            'timezone' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'canonical_url' => 'nullable|string',
        ]);

        $newSlug = Str::slug($validated['slug']);

        // Check if slug changed -> store 301 redirect
        if ($post->slug !== $newSlug && $post->status === 'published') {
            \App\Models\PostSlugRedirect::create([
                'post_id' => $post->id,
                'old_slug' => $post->slug,
            ]);
        }

        $validated['slug'] = $newSlug;
        $wordCount = str_word_count(strip_tags($validated['content'] ?? ''));
        $validated['reading_time'] = max(1, (int) ceil($wordCount / 200));

        if (!empty($validated['published_at'])) {
            $validated['published_at'] = \Carbon\Carbon::parse($validated['published_at']);
        } elseif ($validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        if (isset($validated['tag_ids'])) {
            $post->tags()->sync($validated['tag_ids']);
        }

        // Store version revision snapshot
        \App\Models\PostRevision::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'title' => $post->title,
            'summary' => $post->summary,
            'content' => $post->content,
        ]);

        return redirect()->route('admin.cms.posts.index')->with('success', 'Artikel berhasil diperbarui');
    }

    public function autosave(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
        ]);

        $post->update(array_filter($validated));

        return response()->json([
            'message' => 'Autosaved successfully',
            'saved_at' => now()->toTimeString(),
        ]);
    }

    public function duplicate($id)
    {
        $original = Post::with('tags')->findOrFail($id);

        $duplicate = $original->replicate([
            'slug',
            'view_count',
            'preview_token',
        ]);

        $duplicate->title = 'Copy of ' . $original->title;
        $duplicate->slug = Str::slug($duplicate->title) . '-' . time();
        $duplicate->status = 'draft';
        $duplicate->published_at = null;
        $duplicate->created_at = now();
        $duplicate->save();

        if ($original->tags->isNotEmpty()) {
            $duplicate->tags()->sync($original->tags->pluck('id'));
        }

        return redirect()->route('admin.cms.posts.index')->with('success', 'Artikel berhasil digandakan sebagai draf');
    }

    public function restoreRevision($id, $revisionId)
    {
        $post = Post::findOrFail($id);
        $revision = \App\Models\PostRevision::where('post_id', $id)->findOrFail($revisionId);

        $post->update([
            'title' => $revision->title,
            'summary' => $revision->summary,
            'content' => $revision->content,
        ]);

        return redirect()->back()->with('success', 'Artikel berhasil dipulihkan ke versi revisi tersebut');
    }

    public function calendar()
    {
        $scheduledPosts = Post::where('status', 'scheduled')
            ->orWhereNotNull('published_at')
            ->with(['category', 'author'])
            ->orderBy('published_at', 'asc')
            ->get();

        return Inertia::render('Admin/Cms/Calendar/Index', [
            'scheduledPosts' => $scheduledPosts,
        ]);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->back()->with('success', 'Artikel berhasil dipindahkan ke sampah');
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();

        return redirect()->back()->with('success', 'Artikel berhasil dipulihkan');
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
            Post::whereIn('id', $ids)->delete();
        } elseif ($action === 'restore') {
            Post::onlyTrashed()->whereIn('id', $ids)->restore();
        } else {
            Post::whereIn('id', $ids)->update(['status' => $action]);
        }

        return redirect()->back()->with('success', 'Tindakan massal berhasil dilakukan');
    }
}
