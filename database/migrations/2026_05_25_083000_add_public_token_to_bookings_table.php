<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            if (! Schema::hasColumn('bookings', 'public_token')) {
                $table->string('public_token', 80)->nullable()->unique()->after('booking_code');
            }
        });

        DB::table('bookings')
            ->whereNull('public_token')
            ->orderBy('id')
            ->eachById(function (object $booking): void {
                DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update(['public_token' => Str::random(48)]);
            });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            if (Schema::hasColumn('bookings', 'public_token')) {
                $table->dropUnique(['public_token']);
                $table->dropColumn('public_token');
            }
        });
    }
};
