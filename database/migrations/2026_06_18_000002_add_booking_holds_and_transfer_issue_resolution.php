<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->timestamp('hold_expires_at')->nullable()->index()->after('booking_status');
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->string('resolution_status', 30)->nullable()->index()->after('note');
            $table->text('resolution_note')->nullable()->after('resolution_status');
            $table->unsignedBigInteger('resolved_by')->nullable()->after('resolution_note');
            $table->timestamp('resolved_at')->nullable()->after('resolved_by');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropColumn(['resolution_status', 'resolution_note', 'resolved_by', 'resolved_at']);
        });

        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropColumn('hold_expires_at');
        });
    }
};
