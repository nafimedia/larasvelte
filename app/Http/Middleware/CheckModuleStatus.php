<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleStatus
{
    /**
     * Handle an incoming request and ensure requested module is active.
     */
    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        if (! Module::isActive($moduleKey)) {
            if ($request->wantsJson() || $request->header('X-Inertia')) {
                return back()->with('error', "Modul '{$moduleKey}' saat ini sedang dinonaktifkan oleh Administrator.");
            }

            abort(403, "Modul '{$moduleKey}' saat ini sedang dinonaktifkan.");
        }

        return $next($request);
    }
}
