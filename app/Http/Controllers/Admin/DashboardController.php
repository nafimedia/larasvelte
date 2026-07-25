<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $totalRoles = Role::count();
        $recentActivitiesCount = Activity::where('created_at', '>=', now()->subDays(7))->count();

        $recentActivities = Activity::with('causer')
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'log_name' => $activity->log_name,
                    'description' => $activity->description,
                    'causer_name' => $activity->causer ? $activity->causer->name : 'Sistem',
                    'causer_email' => $activity->causer ? $activity->causer->email : null,
                    'causer_avatar' => $activity->causer ? $activity->causer->avatar_url : null,
                    'created_at' => $activity->created_at->diffForHumans(),
                ];
            });

        $usersByRole = Role::withCount('users')->get()->map(function ($role) {
            return [
                'name' => $role->name,
                'users_count' => $role->users_count,
            ];
        });

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'total_roles' => $totalRoles,
                'recent_activities_count' => $recentActivitiesCount,
            ],
            'recentActivities' => $recentActivities,
            'usersByRole' => $usersByRole,
        ]);
    }
}
