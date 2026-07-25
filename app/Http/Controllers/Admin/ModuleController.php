<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ModuleController extends Controller
{
    /**
     * Display module management settings page.
     */
    public function index(): Response
    {
        $modules = Module::orderBy('order', 'asc')->get();

        return Inertia::render('Admin/Settings/Modules', [
            'modules' => $modules,
        ]);
    }

    /**
     * Toggle active status of a module.
     */
    public function toggle(Request $request, string $key): RedirectResponse
    {
        $module = Module::where('key', $key)->firstOrFail();

        if ($module->is_system) {
            return back()->with('error', 'Modul utama (System Core) tidak dapat dinonaktifkan.');
        }

        $module->update([
            'is_active' => ! $module->is_active,
        ]);

        Module::clearCache();

        $statusText = $module->is_active ? 'diaktifkan' : 'dinonaktifkan';

        activity('modules')
            ->causedBy($request->user())
            ->log("Mengubah status modul '{$module->name}' menjadi {$statusText}");

        return back()->with('success', "Modul '{$module->name}' berhasil {$statusText}!");
    }
}
