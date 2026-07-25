<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageApiController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::where('status', 'published')
            ->where('visibility', 'public')
            ->with('author')
            ->latest()
            ->paginate($request->input('per_page', 10));

        return PageResource::collection($pages);
    }

    public function show($slug)
    {
        $pageItem = Page::where('status', 'published')->where('slug', $slug)->with('author')->firstOrFail();
        $pageItem->increment('view_count');

        return new PageResource($pageItem);
    }
}
