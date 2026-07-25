<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('items')->get();
        if ($menus->isEmpty()) {
            $defaultMenu = Menu::create(['name' => 'Main Navbar', 'location' => 'navbar']);
            MenuItem::create(['menu_id' => $defaultMenu->id, 'title' => 'Home', 'url' => '/', 'type' => 'custom', 'order' => 1]);
            MenuItem::create(['menu_id' => $defaultMenu->id, 'title' => 'Blog', 'url' => '/blog', 'type' => 'custom', 'order' => 2]);
            $menus = Menu::with('items')->get();
        }

        $pages = Page::where('status', 'published')->select('id', 'title', 'slug')->get();
        $posts = Post::where('status', 'published')->select('id', 'title', 'slug')->take(20)->get();
        $categories = Category::select('id', 'name', 'slug')->get();

        return Inertia::render('Admin/Cms/Menus/Index', [
            'menus' => $menus,
            'availablePages' => $pages,
            'availablePosts' => $posts,
            'availableCategories' => $categories,
        ]);
    }

    public function storeMenu(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
        ]);

        Menu::create($validated);

        return redirect()->back()->with('success', 'Menu baru berhasil dibuat');
    }

    public function storeItem(Request $request, $menuId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string',
            'type' => 'required|string',
            'target' => 'nullable|string',
        ]);

        $maxOrder = MenuItem::where('menu_id', $menuId)->max('order') ?? 0;

        MenuItem::create([
            'menu_id' => $menuId,
            'title' => $validated['title'],
            'url' => $validated['url'] ?? '/',
            'type' => $validated['type'],
            'target' => $validated['target'] ?? '_self',
            'order' => $maxOrder + 1,
        ]);

        return redirect()->back()->with('success', 'Item menu berhasil ditambahkan');
    }

    public function destroyItem($id)
    {
        $item = MenuItem::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Item menu berhasil dihapus');
    }
}
