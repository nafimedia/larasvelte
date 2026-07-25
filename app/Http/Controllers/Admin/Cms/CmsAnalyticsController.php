<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CmsAnalyticsController extends Controller
{
    public function index()
    {
        $totalPosts = Post::count();
        $totalPages = Page::count();
        $totalComments = Comment::count();
        $totalCategories = Category::count();

        $topPosts = Post::orderBy('view_count', 'desc')->take(5)->get(['id', 'title', 'slug', 'view_count', 'created_at']);
        $topPages = Page::orderBy('view_count', 'desc')->take(5)->get(['id', 'title', 'slug', 'view_count', 'created_at']);

        $recentComments = Comment::with('post')->latest('id')->take(5)->get();

        return Inertia::render('Admin/Cms/Analytics/Index', [
            'metrics' => [
                'totalPosts' => $totalPosts,
                'totalPages' => $totalPages,
                'totalComments' => $totalComments,
                'totalCategories' => $totalCategories,
            ],
            'topPosts' => $topPosts,
            'topPages' => $topPages,
            'recentComments' => $recentComments,
        ]);
    }
}
