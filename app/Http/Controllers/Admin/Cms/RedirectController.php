<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\RedirectRule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RedirectController extends Controller
{
    public function index()
    {
        $redirects = RedirectRule::latest()->get();

        return Inertia::render('Admin/Cms/Redirects/Index', [
            'redirectRules' => $redirects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'old_url' => 'required|string|unique:redirect_rules,old_url',
            'new_url' => 'required|string',
            'status_code' => 'required|in:301,302',
        ]);

        RedirectRule::create($validated);

        return redirect()->back()->with('success', 'Aturan pengalihan URL (Redirect) berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $rule = RedirectRule::findOrFail($id);
        $rule->delete();

        return redirect()->back()->with('success', 'Aturan pengalihan URL berhasil dihapus');
    }
}
