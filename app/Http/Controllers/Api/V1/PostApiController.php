<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::where('status', 'published')->with(['category', 'tags', 'author']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $posts = $query->latest('published_at')->paginate($request->input('per_page', 10));

        return PostResource::collection($posts);
    }

    public function show($slug)
    {
        $post = Post::where('status', 'published')->where('slug', $slug)->with(['category', 'tags', 'author'])->firstOrFail();
        $post->increment('view_count');

        return new PostResource($post);
    }

    public function categoryPosts($slug, Request $request)
    {
        $posts = Post::where('status', 'published')
            ->whereHas('category', fn($q) => $q->where('slug', $slug))
            ->with(['category', 'tags', 'author'])
            ->latest('published_at')
            ->paginate($request->input('per_page', 10));

        return PostResource::collection($posts);
    }

    public function tagPosts($slug, Request $request)
    {
        $posts = Post::where('status', 'published')
            ->whereHas('tags', fn($q) => $q->where('slug', $slug))
            ->with(['category', 'tags', 'author'])
            ->latest('published_at')
            ->paginate($request->input('per_page', 10));

        return PostResource::collection($posts);
    }

    public function preview($id, $token)
    {
        $post = Post::withTrashed()->with(['category', 'tags', 'author'])->findOrFail($id);

        if ($post->ensurePreviewToken() !== $token) {
            return response()->json(['message' => 'Token preview tidak valid'], 403);
        }

        return new PostResource($post);
    }
}
