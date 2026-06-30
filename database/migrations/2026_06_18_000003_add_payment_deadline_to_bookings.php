<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->timestamp('payment_deadline_at')->nullable()->index()->after('booking_status');
        });

        DB::table('bookings')
            ->whereNotNull('hold_expires_at')
            ->orderBy('id')
            ->each(function (object $booking): void {
                DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update([
                        'payment_deadline_at' => $booking->hold_expires_at,
                        'hold_expires_at' => Carbon::parse($booking->hold_expires_at)->addMinutes(30),
                    ]);
            });

        DB::table('settings')->updateOrInsert(
            ['key_name' => 'booking_admin_grace_minutes'],
            ['value' => '30', 'created_at' => now(), 'updated_at' => now()],
        );
    }

    public function down(): void
    {
        DB::table('settings')->where('key_name', 'booking_admin_grace_minutes')->delete();

        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropColumn('payment_deadline_at');
        });
    }
};
