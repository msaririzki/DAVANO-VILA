<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addon_items', function (Blueprint $table): void {
            $table->string('category')->nullable()->after('type');
        });

        Schema::table('booking_addons', function (Blueprint $table): void {
            $table->string('category')->nullable()->after('type');
        });

        DB::table('addon_items')
            ->where('type', 'extrabed')
            ->update(['category' => 'extra_bed']);

        DB::table('addon_items')
            ->where('type', 'food')
            ->whereNull('category')
            ->update(['category' => 'makanan']);

        DB::table('booking_addons')
            ->where('type', 'extrabed')
            ->update(['category' => 'extra_bed']);

        DB::table('booking_addons')
            ->where('type', 'food')
            ->whereNull('category')
            ->update(['category' => 'makanan']);
    }

    public function down(): void
    {
        Schema::table('booking_addons', function (Blueprint $table): void {
            $table->dropColumn('category');
        });

        Schema::table('addon_items', function (Blueprint $table): void {
            $table->dropColumn('category');
        });
    }
};
