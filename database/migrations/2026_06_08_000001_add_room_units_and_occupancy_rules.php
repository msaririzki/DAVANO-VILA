<?php

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedSmallInteger('included_capacity')->default(2)->after('capacity');
            $table->unsignedSmallInteger('max_capacity')->default(2)->after('included_capacity');
            $table->boolean('allow_unit_quantity')->default(false)->after('max_capacity');
            $table->string('extra_guest_charge_mode')->default('manual')->after('allow_unit_quantity');
            $table->decimal('extra_guest_adult_price', 15, 2)->default(0)->after('extra_guest_charge_mode');
            $table->decimal('extra_guest_child_price', 15, 2)->default(0)->after('extra_guest_adult_price');
            $table->text('capacity_rule_note')->nullable()->after('extra_guest_child_price');
        });

        Schema::create('room_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('status')->default(Room::STATUS_AVAILABLE)->index();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'name']);
        });

        Schema::create('booking_room_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_unit_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['booking_id', 'room_unit_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedSmallInteger('adult_count')->default(1)->after('acquisition_source');
            $table->unsignedSmallInteger('child_count')->default(0)->after('adult_count');
            $table->unsignedSmallInteger('total_guest_count')->default(1)->after('child_count');
            $table->unsignedSmallInteger('unit_count')->default(1)->after('room_id');
            $table->decimal('occupancy_adjustment_amount', 15, 2)->default(0)->after('total_addons_price');
            $table->text('occupancy_adjustment_note')->nullable()->after('occupancy_adjustment_amount');
        });

        DB::table('rooms')->orderBy('id')->get()->each(function (object $room): void {
            $name = Str::lower((string) $room->name);
            $unitCount = Str::contains($name, 'commercial') ? 6 : 1;
            $includedCapacity = Str::contains($name, ['superior', 'besar']) ? 15 : (int) ($room->capacity ?: 2);
            $maxCapacity = Str::contains($name, ['superior', 'besar']) ? 20 : (int) ($room->capacity ?: 2);

            DB::table('rooms')
                ->where('id', $room->id)
                ->update([
                    'included_capacity' => $includedCapacity,
                    'max_capacity' => max($includedCapacity, $maxCapacity),
                    'capacity' => max($includedCapacity, $maxCapacity, (int) ($room->capacity ?: 2)),
                    'allow_unit_quantity' => $unitCount > 1,
                    'extra_guest_charge_mode' => 'manual',
                    'capacity_rule_note' => Str::contains($name, ['superior', 'besar'])
                        ? 'Harga dasar termasuk sampai 15 orang. Jika lebih, biaya tambahan dikonfirmasi admin.'
                        : null,
                ]);

            for ($i = 1; $i <= $unitCount; $i++) {
                DB::table('room_units')->insert([
                    'room_id' => $room->id,
                    'name' => $unitCount > 1 ? sprintf('%s %02d', $room->name, $i) : $room->name.' 01',
                    'status' => $room->status,
                    'is_active' => (bool) $room->is_active,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        DB::table('bookings')->orderBy('id')->get()->each(function (object $booking): void {
            DB::table('bookings')
                ->where('id', $booking->id)
                ->update([
                    'adult_count' => 1,
                    'child_count' => 0,
                    'total_guest_count' => 1,
                    'unit_count' => 1,
                ]);

            if (in_array($booking->payment_status, [Booking::PAYMENT_DP, Booking::PAYMENT_LUNAS], true)) {
                $unitId = DB::table('room_units')
                    ->where('room_id', $booking->room_id)
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->value('id');

                if ($unitId) {
                    DB::table('booking_room_unit')->insertOrIgnore([
                        'booking_id' => $booking->id,
                        'room_unit_id' => $unitId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_room_unit');
        Schema::dropIfExists('room_units');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'adult_count',
                'child_count',
                'total_guest_count',
                'unit_count',
                'occupancy_adjustment_amount',
                'occupancy_adjustment_note',
            ]);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'included_capacity',
                'max_capacity',
                'allow_unit_quantity',
                'extra_guest_charge_mode',
                'extra_guest_adult_price',
                'extra_guest_child_price',
                'capacity_rule_note',
            ]);
        });
    }
};
