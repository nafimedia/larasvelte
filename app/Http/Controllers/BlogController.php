<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->with(['category', 'tags', 'author']);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Tag Filter
        if ($request->filled('tag')) {
            $tagSlug = $request->input('tag');
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'popular') {
            $query->orderBy('view_count', 'desc');
        } elseif ($sort === 'oldest') {
            $query->orderBy('published_at', 'asc');
        } else {
            $query->orderBy('is_sticky', 'desc')
                  ->orderBy('published_at', 'desc');
        }

        $posts = $query->paginate(9)->withQueryString();

        // Featured & Popular Articles for Widgets
        $featuredPost = Post::where('status', 'published')->where('is_featured', true)->with(['category', 'author'])->first();
        $popularPosts = Post::where('status', 'published')->orderBy('view_count', 'desc')->take(5)->get(['id', 'title', 'slug', 'featured_image', 'view_count', 'published_at']);
        $categories = Category::withCount('posts')->get();
        $popularTags = Tag::withCount('posts')->orderBy('posts_count', 'desc')->take(10)->get();

        return \Inertia\Inertia::render('Blog/Index', [
            'posts' => $posts,
            'featuredPost' => $featuredPost,
            'popularPosts' => $popularPosts,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'filters' => $request->only(['search', 'category', 'tag', 'sort']),
        ]);
    }

    public function show($slug, Request $request)
    {
        // 1. Check if slug exists in old slug redirects (301 Permanent Redirect)
        $redirect = \App\Models\PostSlugRedirect::where('old_slug', $slug)->with('post')->first();
        if ($redirect && $redirect->post && $redirect->post->status === 'published') {
            return redirect()->to(route('blog.show', $redirect->post->slug), 301);
        }

        $post = Post::where('status', 'published')
            ->where('slug', $slug)
            ->with(['category', 'tags', 'author', 'comments' => function ($q) {
                $q->where('status', 'approved')->whereNull('parent_id')->with('replies');
            }])
            ->firstOrFail();

        // View Tracking (Deduplication per session)
        $sessionKey = 'viewed_post_' . $post->id;
        if (!session()->has($sessionKey)) {
            $post->increment('view_count');
            session()->put($sessionKey, true);
        }

        // Table of Contents (TOC) Extraction
        $toc = [];
        if (!empty($post->content)) {
            preg_match_all('/<h([2-3])\b[^>]*>(.*?)<\/h[2-3]>/is', $post->content, $matches, PREG_SET_ORDER);
            foreach ($matches as $index => $match) {
                $level = (int) $match[1];
                $title = strip_tags($match[2]);
                $id = 'heading-' . ($index + 1);
                $toc[] = [
                    'id' => $id,
                    'level' => $level,
                    'title' => $title,
                ];
            }
        }

        // Related Articles (Prioritize manual_related_ids if available, otherwise category/tags)
        if (!empty($post->manual_related_ids) && is_array($post->manual_related_ids)) {
            $relatedPosts = Post::where('status', 'published')
                ->whereIn('id', $post->manual_related_ids)
                ->take(3)
                ->get();
        } else {
            $relatedPosts = Post::where('status', 'published')
                ->where('id', '!=', $post->id)
                ->where(function ($q) use ($post) {
                    if ($post->category_id) {
                        $q->where('category_id', $post->category_id);
                    }
                    if ($post->tags->isNotEmpty()) {
                        $q->orWhereHas('tags', fn($t) => $t->whereIn('tags.id', $post->tags->pluck('id')));
                    }
                })
                ->latest('published_at')
                ->take(3)
                ->get();
        }

        $popularPosts = Post::where('status', 'published')->orderBy('view_count', 'desc')->take(5)->get(['id', 'title', 'slug', 'featured_image', 'view_count', 'published_at']);
        $categories = Category::withCount('posts')->get();
        $popularTags = Tag::withCount('posts')->orderBy('posts_count', 'desc')->take(10)->get();

        return \Inertia\Inertia::render('Blog/Show', [
            'post' => $post,
            'toc' => $toc,
            'relatedPosts' => $relatedPosts,
            'popularPosts' => $popularPosts,
            'categories' => $categories,
            'popularTags' => $popularTags,
        ]);
    }

    public function preview($id, $token)
    {
        $post = Post::withTrashed()->with(['category', 'tags', 'author'])->findOrFail($id);

        if ($post->ensurePreviewToken() !== $token) {
            abort(403, 'Tautan preview tidak valid atau token telah kadaluarsa.');
        }

        return \Inertia\Inertia::render('Blog/Preview', [
            'post' => $post,
            'previewToken' => $token,
        ]);
    }

    public function storeComment(Request $request, $id)
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:255',
            'author_email' => 'required|email|max:255',
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $post = Post::where('status', 'published')->findOrFail($id);

        Comment::create([
            'post_id' => $post->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => auth()->check() ? auth()->id() : null,
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'],
            'content' => $validated['content'],
            'status' => 'approved',
        ]);

        return redirect()->back()->with('success', 'Komentar Anda berhasil terkirim');
    }
}
