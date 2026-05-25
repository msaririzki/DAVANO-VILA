<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicMediaSettingController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_media_mode' => ['required', 'in:photos,video'],
        ]);

        Setting::query()->updateOrCreate(
            ['key_name' => 'hero_media_mode'],
            ['value' => $validated['hero_media_mode']],
        );

        return back()->with('status', 'Pengaturan media halaman publik berhasil diperbarui.');
    }
}
