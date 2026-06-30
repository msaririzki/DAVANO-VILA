<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->string('proof_path')->nullable()->after('note');
            $table->string('proof_sha256', 64)->nullable()->unique()->after('proof_path');
            $table->unsignedTinyInteger('ocr_confidence')->nullable()->after('proof_sha256');
            $table->decimal('ocr_detected_amount', 15, 2)->nullable()->after('ocr_confidence');
            $table->string('ocr_detected_reference', 120)->nullable()->after('ocr_detected_amount');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropUnique(['proof_sha256']);
            $table->dropColumn([
                'proof_path',
                'proof_sha256',
                'ocr_confidence',
                'ocr_detected_amount',
                'ocr_detected_reference',
            ]);
        });
    }
};
