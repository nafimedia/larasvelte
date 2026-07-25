<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $logName = $request->input('log_name');
        $perPage = (int) $request->input('per_page', 15);

        $query = Activity::with('causer', 'subject');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHasMorph('causer', ['App\Models\User'], function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($logName) {
            $query->where('log_name', $logName);
        }

        $activities = $query->latest()->paginate($perPage)->withQueryString();

        $activities->getCollection()->transform(function ($activity) {
            return [
                'id' => $activity->id,
                'log_name' => $activity->log_name,
                'description' => $activity->description,
                'subject_type' => $activity->subject_type ? class_basename($activity->subject_type) : null,
                'subject_id' => $activity->subject_id,
                'causer_name' => $activity->causer ? $activity->causer->name : 'System/Guest',
                'causer_email' => $activity->causer ? $activity->causer->email : null,
                'causer_avatar' => $activity->causer ? $activity->causer->avatar_url : null,
                'properties' => $activity->properties,
                'created_at' => $activity->created_at->format('d M Y, H:i:s'),
                'created_at_human' => $activity->created_at->diffForHumans(),
            ];
        });

        $logNames = Activity::distinct()->pluck('log_name');

        return Inertia::render('Admin/ActivityLogs/Index', [
            'activities' => $activities,
            'logNames' => $logNames,
            'filters' => [
                'search' => $search ?? '',
                'log_name' => $logName ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    public function clear(Request $request): RedirectResponse
    {
        Activity::truncate();

        activity('system')
            ->causedBy($request->user())
            ->log('Pembersihan seluruh riwayat Log Aktivitas');

        return back()->with('success', 'Seluruh riwayat log aktivitas telah dibersihkan.');
    }
}
