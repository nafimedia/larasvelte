<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with(['post', 'user', 'replies']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%")
                  ->orWhere('author_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'trash') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        $comments = $query->latest('id')->paginate($request->input('per_page', 10))->withQueryString();

        return Inertia::render('Admin/Cms/Comments/Index', [
            'comments' => $comments,
            'filters' => $request->only(['search', 'status', 'per_page']),
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,spam,trash',
        ]);

        $comment = Comment::withTrashed()->findOrFail($id);

        if ($validated['status'] === 'trash') {
            $comment->delete();
        } else {
            if ($comment->trashed()) {
                $comment->restore();
            }
            $comment->update(['status' => $validated['status']]);
        }

        return redirect()->back()->with('success', 'Status komentar diperbarui');
    }

    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $parent = Comment::findOrFail($id);

        Comment::create([
            'post_id' => $parent->post_id,
            'parent_id' => $parent->id,
            'user_id' => auth()->id(),
            'author_name' => auth()->user()->name,
            'author_email' => auth()->user()->email,
            'content' => $validated['content'],
            'status' => 'approved',
        ]);

        return redirect()->back()->with('success', 'Balasan komentar berhasil dikirim');
    }

    public function destroy($id)
    {
        $comment = Comment::withTrashed()->findOrFail($id);
        $comment->forceDelete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus permanen');
    }
}
