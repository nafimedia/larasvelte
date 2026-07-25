<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\Cms\CategoryController;
use App\Http\Controllers\Admin\Cms\CmsAnalyticsController;
use App\Http\Controllers\Admin\Cms\CommentController;
use App\Http\Controllers\Admin\Cms\PageController;
use App\Http\Controllers\Admin\Cms\PostController;
use App\Http\Controllers\Admin\Cms\TagController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LandingBuilderController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BrandingController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\Cms\MenuController;
use App\Http\Controllers\Admin\Cms\MediaController;
use App\Http\Controllers\Admin\Cms\FormBuilderController;
use App\Http\Controllers\Admin\Cms\RedirectController;
use Illuminate\Support\Facades\Route;

// Home Route (Landing Page)
Route::get('/', function () {
    $sections = \App\Models\LandingSection::where('is_active', true)
        ->where('status', 'published')
        ->orderBy('order', 'asc')
        ->get();

    $theme = \App\Models\LandingSiteSetting::get('theme_config');
    $seo = \App\Models\LandingSiteSetting::get('seo_config');

    $latestPosts = \App\Models\Post::where('status', 'published')
        ->where(function ($q) {
            $q->whereNull('published_at')->orWhere('published_at', '<=', now());
        })
        ->with(['category', 'author'])
        ->latest('published_at')
        ->take(3)
        ->get();

    $navMenu = \App\Models\Menu::where('location', 'navbar')
        ->where('is_active', true)
        ->with(['items' => function ($q) {
            $q->orderBy('order', 'asc');
        }])
        ->first();

    return \Inertia\Inertia::render('Welcome', [
        'dynamicSections' => $sections,
        'themeSettings' => $theme,
        'seoSettings' => $seo,
        'latestPosts' => $latestPosts,
        'navMenu' => $navMenu,
    ]);
})->name('welcome');

// Public Blog Routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/preview/posts/{id}/{token}', [BlogController::class, 'preview'])->name('blog.preview');
Route::post('/blog/{id}/comments', [BlogController::class, 'storeComment'])->name('blog.comments.store');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Admin Dashboard & Management
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Landing Page Builder Studio
        Route::middleware('module:landing_builder')->group(function () {
            Route::get('/landing-builder', [LandingBuilderController::class, 'index'])->name('landing-builder.index');
            Route::post('/landing-builder/sections', [LandingBuilderController::class, 'storeSection'])->name('landing-builder.sections.store');
            Route::put('/landing-builder/sections/reorder', [LandingBuilderController::class, 'reorderSections'])->name('landing-builder.sections.reorder');
            Route::put('/landing-builder/sections/{id}', [LandingBuilderController::class, 'updateSection'])->name('landing-builder.sections.update');
            Route::post('/landing-builder/sections/{id}/duplicate', [LandingBuilderController::class, 'duplicateSection'])->name('landing-builder.sections.duplicate');
            Route::delete('/landing-builder/sections/{id}', [LandingBuilderController::class, 'destroySection'])->name('landing-builder.sections.destroy');
            Route::post('/landing-builder/publish', [LandingBuilderController::class, 'publish'])->name('landing-builder.publish');
            Route::post('/landing-builder/settings', [LandingBuilderController::class, 'updateSettings'])->name('landing-builder.settings');
            Route::post('/landing-builder/media', [LandingBuilderController::class, 'uploadMedia'])->name('landing-builder.media');
        });

        // Modern CMS Module Routes
        Route::prefix('cms')->name('cms.')->group(function () {
            // Pages Management
            Route::middleware('module:pages')->group(function () {
                Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
                Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
                Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
                Route::get('/pages/{id}/edit', [PageController::class, 'edit'])->name('pages.edit');
                Route::put('/pages/{id}', [PageController::class, 'update'])->name('pages.update');
                Route::delete('/pages/{id}', [PageController::class, 'destroy'])->name('pages.destroy');
                Route::post('/pages/{id}/restore', [PageController::class, 'restore'])->name('pages.restore');
                Route::post('/pages/bulk', [PageController::class, 'bulkAction'])->name('pages.bulk');
            });

            // Posts (Articles) Management
            Route::middleware('module:posts')->group(function () {
                Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
                Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
                Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
                Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
                Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
                Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
                Route::post('/posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
                Route::post('/posts/{id}/autosave', [PostController::class, 'autosave'])->name('posts.autosave');
                Route::post('/posts/{id}/duplicate', [PostController::class, 'duplicate'])->name('posts.duplicate');
                Route::post('/posts/{id}/revisions/{revisionId}/restore', [PostController::class, 'restoreRevision'])->name('posts.revisions.restore');
                Route::post('/posts/bulk', [PostController::class, 'bulkAction'])->name('posts.bulk');
                Route::get('/calendar', [PostController::class, 'calendar'])->name('calendar.index');
            });

            // Categories & Tags
            Route::middleware('module:categories')->group(function () {
                Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
                Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
                Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
                Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

                Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
                Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
                Route::put('/tags/{id}', [TagController::class, 'update'])->name('tags.update');
                Route::delete('/tags/{id}', [TagController::class, 'destroy'])->name('tags.destroy');
            });

            // Comments Moderation
            Route::middleware('module:comments')->group(function () {
                Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
                Route::patch('/comments/{id}/status', [CommentController::class, 'updateStatus'])->name('comments.status');
                Route::post('/comments/{id}/reply', [CommentController::class, 'reply'])->name('comments.reply');
                Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
            });

            // Menu Management
            Route::middleware('module:menus')->group(function () {
                Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
                Route::post('/menus', [MenuController::class, 'storeMenu'])->name('menus.store');
                Route::post('/menus/{menuId}/items', [MenuController::class, 'storeItem'])->name('menus.items.store');
                Route::delete('/menus/items/{id}', [MenuController::class, 'destroyItem'])->name('menus.items.destroy');
            });

            // Media Library Asset Manager
            Route::middleware('module:media')->group(function () {
                Route::get('/media', [MediaController::class, 'index'])->name('media.index');
                Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
                Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');
            });

            // Dynamic Form Builder
            Route::middleware('module:forms')->group(function () {
                Route::get('/forms', [FormBuilderController::class, 'index'])->name('forms.index');
                Route::post('/forms', [FormBuilderController::class, 'store'])->name('forms.store');
                Route::get('/forms/{id}/submissions', [FormBuilderController::class, 'submissions'])->name('forms.submissions');
            });

            // Redirect Manager 301/302
            Route::middleware('module:redirects')->group(function () {
                Route::get('/redirects', [RedirectController::class, 'index'])->name('redirects.index');
                Route::post('/redirects', [RedirectController::class, 'store'])->name('redirects.store');
                Route::delete('/redirects/{id}', [RedirectController::class, 'destroy'])->name('redirects.destroy');
            });

            // CMS Analytics
            Route::middleware('module:analytics')->group(function () {
                Route::get('/analytics', [CmsAnalyticsController::class, 'index'])->name('analytics.index');
            });
        });

// Public Form Submission Route
Route::middleware('module:forms')->group(function () {
    Route::post('/forms/{slug}/submit', [FormBuilderController::class, 'submitPublicForm'])->name('forms.public.submit');
});

        // Users Management
        Route::middleware('permission:users.view')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
            Route::post('/users/{user}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('users.update');
            Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('permission:users.edit')->name('users.toggle-status');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');
        });

        // Roles & Permissions RBAC
        Route::middleware('permission:roles.view')->group(function () {
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('roles.store');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.edit')->name('roles.update');
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('roles.destroy');
        });

        // Activity Logs
        Route::middleware(['permission:activity_logs.view', 'module:activity_logs'])->group(function () {
            Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
            Route::delete('/activity-logs/clear', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');
        });

        // Site Settings & Branding Management
        Route::middleware('permission:settings.view')->group(function () {
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::put('/settings', [SettingController::class, 'update'])->middleware('permission:settings.edit')->name('settings.update');

            // Branding Management
            Route::get('/settings/branding', [BrandingController::class, 'index'])->name('settings.branding.index');
            Route::post('/settings/branding/upload', [BrandingController::class, 'upload'])->middleware('permission:settings.edit')->name('settings.branding.upload');
            Route::delete('/settings/branding/{key}', [BrandingController::class, 'destroy'])->middleware('permission:settings.edit')->name('settings.branding.destroy');

            // Module Management
            Route::get('/settings/modules', [ModuleController::class, 'index'])->middleware('permission:modules.view')->name('settings.modules.index');
            Route::patch('/settings/modules/{key}/toggle', [ModuleController::class, 'toggle'])->middleware('permission:modules.edit')->name('settings.modules.toggle');
        });
    });
});
