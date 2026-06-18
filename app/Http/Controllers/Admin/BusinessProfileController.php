<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\AuditLogger;
use App\Support\BusinessProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BusinessProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.business-profile', [
            'profile' => BusinessProfile::all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $number = preg_replace('/\D+/', '', $request->string('villa_whatsapp_number')->toString()) ?? '';
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }
        $request->merge(['villa_whatsapp_number' => $number]);

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:100'],
            'business_tagline' => ['nullable', 'string', 'max:160'],
            'business_description' => ['nullable', 'string', 'max:1000'],
            'business_address' => ['nullable', 'string', 'max:500'],
            'business_maps_url' => ['nullable', 'url:http,https', 'max:1000'],
            'business_email' => ['nullable', 'email', 'max:255'],
            'villa_whatsapp_number' => ['required', 'digits_between:10,15', 'regex:/^62[0-9]+$/'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:1000'],
            'tiktok_url' => ['nullable', 'url:http,https', 'max:1000'],
            'threads_url' => ['nullable', 'url:http,https', 'max:1000'],
            'facebook_url' => ['nullable', 'url:http,https', 'max:1000'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['required', 'date_format:H:i'],
        ], [
            'villa_whatsapp_number.regex' => 'Nomor WhatsApp harus menggunakan nomor Indonesia yang diawali 08 atau 62.',
        ]);

        $oldValues = BusinessProfile::all();

        DB::transaction(function () use ($validated): void {
            foreach ($validated as $key => $value) {
                Setting::query()->updateOrCreate(
                    ['key_name' => $key],
                    ['value' => $value ?? ''],
                );
            }
        });

        $setting = Setting::query()->where('key_name', 'business_name')->first();
        AuditLogger::record(
            $request,
            'setting.business_profile_updated',
            'Memperbarui profil bisnis dan kontak publik',
            $setting,
            $oldValues,
            BusinessProfile::all(),
        );

        return back()->with('status', 'Profil bisnis berhasil diperbarui.');
    }
}
