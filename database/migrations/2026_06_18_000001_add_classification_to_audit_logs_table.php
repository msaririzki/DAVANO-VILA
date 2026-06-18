<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table): void {
            $table->string('category')->default('operational')->after('action');
            $table->boolean('is_financial')->default(false)->after('category');
            $table->index(['is_financial', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table): void {
            $table->dropIndex(['is_financial', 'created_at']);
            $table->dropColumn(['category', 'is_financial']);
        });
    }
};
