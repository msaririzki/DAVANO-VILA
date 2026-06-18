<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VillaContactSettingController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $number = preg_replace('/\D+/', '', $request->string('villa_whatsapp_number')->toString()) ?? '';

        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        $request->merge(['villa_whatsapp_number' => $number]);

        $validated = $request->validate([
            'villa_whatsapp_number' => ['required', 'digits_between:10,15', 'regex:/^62[0-9]+$/'],
        ], [
            'villa_whatsapp_number.regex' => 'Nomor WhatsApp harus menggunakan nomor Indonesia yang diawali 08 atau 62.',
        ]);

        $oldValue = Setting::value('villa_whatsapp_number', '6280000000000');
        $setting = Setting::query()->updateOrCreate(
            ['key_name' => 'villa_whatsapp_number'],
            ['value' => $validated['villa_whatsapp_number']],
        );

        AuditLogger::record(
            $request,
            'setting.whatsapp_updated',
            'Mengubah nomor WhatsApp Admin',
            $setting,
            ['villa_whatsapp_number' => $oldValue],
            ['villa_whatsapp_number' => $validated['villa_whatsapp_number']],
        );

        return back()->with('status', 'Nomor WhatsApp Admin berhasil diperbarui.');
    }
}
