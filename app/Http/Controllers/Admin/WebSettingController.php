<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddonItem;
use App\Models\BankAccount;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\View\View;

class WebSettingController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.web-settings', [
            'heroMediaMode' => Setting::value('hero_media_mode', 'photos'),
            'minDpPercent' => (int) Setting::value('min_dp_percent', 50),
            'bankAccounts' => BankAccount::query()->orderBy('bank_name')->get(),
            'rooms' => Room::query()->orderBy('name')->get(),
            'addonItems' => AddonItem::query()->orderBy('name')->get(),
        ]);
    }
}
