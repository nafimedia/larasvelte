<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BrandingController extends Controller
{
    public const CACHE_KEY = 'site_branding_settings';

    /**
     * Display the Branding Management page.
     */
    public function index(): Response
    {
        $brandingKeys = [
            'public_logo_light',
            'public_logo_dark',
            'public_logo_mobile',
            'public_logo_footer',
            'public_favicon',
            'public_apple_touch_icon',
            'admin_logo_light',
            'admin_logo_dark',
            'admin_logo_collapsed',
            'admin_favicon',
            'admin_login_logo',
        ];

        $settings = SiteSetting::whereIn('key', $brandingKeys)->get()->keyBy('key');

        $branding = [];
        foreach ($brandingKeys as $key) {
            $value = $settings->get($key)?->value;
            $url = $value ? (str_starts_with($value, 'http') ? $value : Storage::disk('public')->url($value)) : null;
            $branding[$key] = [
                'key' => $key,
                'value' => $value,
                'url' => $url,
                'label' => $settings->get($key)?->label ?? $key,
                'description' => $settings->get($key)?->description ?? '',
            ];
        }

        return Inertia::render('Admin/Settings/Branding', [
            'branding' => $branding,
        ]);
    }

    /**
     * Upload & replace a branding asset (Logo / Favicon).
     */
    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'key' => ['required', 'string', 'in:public_logo_light,public_logo_dark,public_logo_mobile,public_logo_footer,public_favicon,public_apple_touch_icon,admin_logo_light,admin_logo_dark,admin_logo_collapsed,admin_favicon,admin_login_logo'],
            'file' => ['required', 'file', 'mimes:png,jpg,jpeg,svg,webp,ico', 'max:2048'], // Max 2MB
        ]);

        $key = $request->input('key');
        $file = $request->file('file');

        // Retrieve existing setting to delete old asset if exists
        $setting = SiteSetting::where('key', $key)->first();

        if ($setting && $setting->value) {
            if (Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }
        }

        // Store new asset
        $filename = $key . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('branding', $filename, 'public');

        SiteSetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $path,
                'group' => 'branding',
                'type' => 'string',
                'label' => $setting?->label ?? ucfirst(str_replace('_', ' ', $key)),
            ]
        );

        // Invalidate branding cache
        Cache::forget(self::CACHE_KEY);

        activity('branding')
            ->causedBy($request->user())
            ->log("Memperbarui Aset Branding: {$key}");

        return back()->with('success', 'Aset branding berhasil diperbarui!');
    }

    /**
     * Delete an uploaded branding asset.
     */
    public function destroy(Request $request, string $key): RedirectResponse
    {
        $setting = SiteSetting::where('key', $key)->firstOrFail();

        if ($setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting->update(['value' => null]);

        // Invalidate branding cache
        Cache::forget(self::CACHE_KEY);

        activity('branding')
            ->causedBy($request->user())
            ->log("Menghapus Aset Branding: {$key}");

        return back()->with('success', 'Aset branding berhasil dihapus!');
    }

    /**
     * Helper to retrieve cached branding array with public URLs.
     */
    public static function getCachedBranding(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $brandingKeys = [
                'public_logo_light',
                'public_logo_dark',
                'public_logo_mobile',
                'public_logo_footer',
                'public_favicon',
                'public_apple_touch_icon',
                'admin_logo_light',
                'admin_logo_dark',
                'admin_logo_collapsed',
                'admin_favicon',
                'admin_login_logo',
            ];

            $settings = SiteSetting::whereIn('key', $brandingKeys)->pluck('value', 'key')->all();

            $result = [];
            foreach ($brandingKeys as $key) {
                $val = $settings[$key] ?? null;
                $result[$key] = $val ? (str_starts_with($val, 'http') ? $val : Storage::disk('public')->url($val)) : null;
            }

            return $result;
        });
    }
}
