<?php

namespace App\Support;

use App\Models\Setting;

class BusinessProfile
{
    /** @return array<string, string> */
    public static function defaults(): array
    {
        return [
            'business_name' => 'Villa Dafano',
            'business_tagline' => 'Penginapan nyaman di Sembalun',
            'business_description' => '',
            'business_address' => '',
            'business_maps_url' => '',
            'business_email' => '',
            'villa_whatsapp_number' => '6280000000000',
            'instagram_url' => 'https://www.instagram.com/villadafanosembalun/',
            'tiktok_url' => 'https://www.tiktok.com/@dafanovillasembalun',
            'threads_url' => 'https://www.threads.net/@villadafanosembalun',
            'facebook_url' => 'https://www.facebook.com/share/1EKggZ2LNC',
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
        ];
    }

    /** @return array<string, string> */
    public static function all(): array
    {
        $defaults = self::defaults();
        $stored = Setting::query()
            ->whereIn('key_name', array_keys($defaults))
            ->pluck('value', 'key_name')
            ->all();

        return array_replace($defaults, $stored);
    }
}
