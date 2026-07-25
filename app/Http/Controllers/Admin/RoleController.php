<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::with('permissions', 'users')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'users_count' => $role->users->count(),
                'permissions' => $role->permissions->pluck('name'),
                'created_at' => $role->created_at->format('d M Y'),
            ];
        });

        $permissions = Permission::all()->map(function ($perm) {
            return [
                'id' => $perm->id,
                'name' => $perm->name,
            ];
        });

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'allPermissions' => $permissions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        activity('role_management')
            ->causedBy($request->user())
            ->performedOn($role)
            ->log("Membuat Peran (Role) baru: {$role->name}");

        return back()->with('success', "Role {$role->name} berhasil dibuat!");
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        activity('role_management')
            ->causedBy($request->user())
            ->performedOn($role)
            ->log("Memperbarui Peran & Izin: {$role->name}");

        return back()->with('success', "Role {$role->name} dan izin berhasil diperbarui!");
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        if (in_array($role->name, ['Super Admin', 'Admin', 'User'])) {
            return back()->with('error', "Role bawaan sistem '{$role->name}' tidak dapat dihapus.");
        }

        $roleName = $role->name;
        $role->delete();

        activity('role_management')
            ->causedBy($request->user())
            ->log("Menghapus Role: {$roleName}");

        return back()->with('success', "Role {$roleName} berhasil dihapus.");
    }
}
