<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogger
{
    /** @var list<string> */
    private const FINANCIAL_ACTIONS = [
        'payment.',
        'addon_payment.',
        'booking.adjusted',
        'booking.cancelled',
        'booking.created_',
        'booking_addon.',
        'bank_account.',
        'room.created',
        'room.updated',
        'addon_item.',
    ];

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public static function record(
        Request $request,
        string $action,
        string $summary,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): void {
        $isFinancial = collect(self::FINANCIAL_ACTIONS)
            ->contains(fn (string $financialAction): bool => str_starts_with($action, $financialAction));

        AuditLog::query()->create([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'category' => self::categoryFor($action, $isFinancial),
            'is_financial' => $isFinancial,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'summary' => $summary,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    private static function categoryFor(string $action, bool $isFinancial): string
    {
        if ($isFinancial) {
            return 'financial';
        }

        if (str_starts_with($action, 'auth.') || str_starts_with($action, 'user.')) {
            return 'security';
        }

        if (str_starts_with($action, 'setting.') || str_starts_with($action, 'room.') || str_starts_with($action, 'addon_item.')) {
            return 'master_data';
        }

        return 'operational';
    }
}
