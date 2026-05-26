<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicMediaSettingController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_media_mode' => ['required', 'in:photos,video'],
        ]);

        $oldValue = Setting::value('hero_media_mode', 'photos');

        $setting = Setting::query()->updateOrCreate(
            ['key_name' => 'hero_media_mode'],
            ['value' => $validated['hero_media_mode']],
        );

        AuditLogger::record(
            $request,
            'setting.updated',
            'Mengubah mode hero halaman publik menjadi '.$validated['hero_media_mode'],
            $setting,
            ['hero_media_mode' => $oldValue],
            ['hero_media_mode' => $validated['hero_media_mode']],
        );

        return back()->with('status', 'Pengaturan media halaman publik berhasil diperbarui.');
    }
}
