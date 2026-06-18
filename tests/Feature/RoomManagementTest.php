<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_super_admin_can_access_room_master(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)
            ->get(route('rooms.index'))
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->get(route('rooms.index'))
            ->assertOk()
            ->assertSee('Daftar Tipe Kamar');

        $room = Room::query()->create([
            'name' => 'Suite Test',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('rooms.create'))
            ->assertOk()
            ->assertSee('Tambah kamar');

        $this->actingAs($superAdmin)
            ->get(route('rooms.edit', $room))
            ->assertOk()
            ->assertSee('Edit kamar');
    }

    public function test_super_admin_can_create_and_update_room_with_public_facilities(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->post(route('rooms.store'), [
                'name' => 'Suite Mountain',
                'description' => 'Kamar luas dengan pemandangan bukit.',
                'price' => 750000,
                'capacity' => 3,
                'status' => Room::STATUS_AVAILABLE,
                'facilities_text' => "King bed\nHot water\nWi-Fi",
                'image_url' => 'https://example.com/room.jpg',
                'is_active' => '1',
            ])
            ->assertRedirect(route('rooms.index'));

        $room = Room::query()->where('name', 'Suite Mountain')->firstOrFail();

        $this->assertSame(['King bed', 'Hot water', 'Wi-Fi'], $room->facilities);
        $this->assertSame('https://example.com/room.jpg', $room->image_path);

        $this->actingAs($superAdmin)
            ->patch(route('rooms.update', $room), [
                'name' => 'Suite Mountain View',
                'description' => 'Kamar luas dengan pemandangan bukit.',
                'price' => 800000,
                'capacity' => 4,
                'status' => Room::STATUS_AVAILABLE,
                'facilities_text' => "King bed\nPrivate bathroom\nBreakfast area",
                'is_active' => '1',
            ])
            ->assertRedirect(route('rooms.index'));

        $room->refresh();

        $this->assertSame('Suite Mountain View', $room->name);
        $this->assertSame(['King bed', 'Private bathroom', 'Breakfast area'], $room->facilities);
        $this->assertSame('https://example.com/room.jpg', $room->image_path);
    }

    public function test_public_room_results_show_room_facilities(): void
    {
        Room::query()->create([
            'name' => 'Suite Mountain',
            'description' => 'Kamar luas dengan pemandangan bukit.',
            'price' => 750000,
            'capacity' => 3,
            'status' => Room::STATUS_AVAILABLE,
            'facilities' => ['Hot water', 'Mountain view'],
            'is_active' => true,
        ]);

        $this->withHeader('Accept-Language', 'id-ID,id;q=0.9')
            ->get(route('public.rooms.index', [
                'check_in_date' => now()->addDay()->toDateString(),
                'check_out_date' => now()->addDays(2)->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Fasilitas')
            ->assertSee('Hot water')
            ->assertSee('Mountain view');
    }

    public function test_super_admin_can_enable_public_hero_video_mode(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->patch(route('settings.public-media.update'), [
                'hero_media_mode' => 'video',
            ])
            ->assertRedirect();

        $this->assertSame('video', Setting::value('hero_media_mode'));

        $this->get(route('public.home'))
            ->assertOk()
            ->assertSee('data-enabled="1"', false)
            ->assertSee('dafano-media/video/vidio.mp4');
    }

    public function test_super_admin_can_open_separated_settings_and_reports_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)
            ->get(route('admin.web-settings'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.reports'))
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->get(route('admin.web-settings'))
            ->assertOk()
            ->assertSee('Kontrol Halaman Publik');

        $this->actingAs($superAdmin)
            ->get(route('admin.reports'))
            ->assertOk()
            ->assertSee('Ringkasan Bisnis');
    }

    public function test_only_super_admin_can_manage_bank_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $bankAccount = BankAccount::query()->create([
            'bank_name' => 'BCA',
            'account_number' => '0562603148',
            'account_name' => 'PT DAFFAVANORAFFASYA',
            'is_active' => true,
        ]);

        $payload = [
            'bank_name' => 'Mandiri',
            'account_number' => '1610016660446',
            'account_name' => 'PT DAFFAVANORAFFASYA',
            'is_active' => '1',
        ];

        $this->actingAs($admin)
            ->post(route('bank-accounts.store'), $payload)
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('bank-accounts.update', $bankAccount), $payload)
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->post(route('bank-accounts.store'), $payload)
            ->assertRedirect(route('password.confirm'));

        $this->actingAs($superAdmin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('bank-accounts.store'), $payload)
            ->assertRedirect(route('admin.web-settings'));

        $this->assertDatabaseHas('bank_accounts', [
            'bank_name' => 'Mandiri',
            'account_number' => '1610016660446',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $superAdmin->id,
            'action' => 'bank_account.created',
            'summary' => 'Menambahkan rekening Mandiri 1610016660446',
        ]);

        $this->actingAs($superAdmin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->patch(route('bank-accounts.update', $bankAccount), [
                'bank_name' => 'BCA',
                'account_number' => '0562603148',
                'account_name' => 'PT DAFFAVANORAFFASYA',
            ])
            ->assertRedirect(route('admin.web-settings'));

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bankAccount->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $superAdmin->id,
            'action' => 'bank_account.updated',
            'summary' => 'Mengubah rekening BCA 0562603148',
        ]);
    }

    public function test_super_admin_can_view_audit_logs_and_logs_are_append_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $auditLog = AuditLog::query()->create([
            'user_id' => $superAdmin->id,
            'action' => 'test.action',
            'summary' => 'Log pengujian',
            'old_values' => ['status' => 'lama'],
            'new_values' => ['status' => 'baru'],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.audit-logs'))
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->get(route('admin.audit-logs'))
            ->assertOk()
            ->assertSee('Riwayat Aktivitas Penting')
            ->assertSee('Log pengujian');

        $this->expectException(\LogicException::class);
        $auditLog->update(['summary' => 'Diubah']);
    }
}
