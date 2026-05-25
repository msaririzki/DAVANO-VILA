<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_can_switch_language_manually(): void
    {
        $this->get('/?lang=en')
            ->assertRedirect('/');

        $this->get('/')
            ->assertOk()
            ->assertSee('Search rooms')
            ->assertSee('Choose an available room');
    }

    public function test_public_page_detects_browser_language(): void
    {
        $this->withHeader('Accept-Language', 'zh-CN,zh;q=0.9,en;q=0.8')
            ->get('/')
            ->assertOk()
            ->assertSee('搜索房间')
            ->assertSee('选择可预订房间');
    }

    public function test_public_page_falls_back_to_indonesian(): void
    {
        $this->withHeader('Accept-Language', 'fr-FR,fr;q=0.9')
            ->get('/')
            ->assertOk()
            ->assertSee('Cari kamar')
            ->assertSee('Pilih kamar yang tersedia');
    }

    public function test_public_validation_messages_follow_selected_language(): void
    {
        $this->withHeader('Accept-Language', 'id-ID,id;q=0.9')
            ->from(route('public.rooms.index'))
            ->get(route('public.rooms.index', [
                'check_in_date' => now()->subDay()->toDateString(),
                'check_out_date' => now()->addDay()->toDateString(),
            ]))
            ->assertRedirect(route('public.rooms.index'))
            ->assertSessionHasErrors([
                'check_in_date' => 'tanggal check-in harus hari ini atau setelahnya.',
            ]);
    }

    public function test_home_search_query_redirects_to_rooms_page(): void
    {
        $this->get(route('public.home', [
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
        ]))
            ->assertRedirect(route('public.rooms.index', [
                'check_in_date' => now()->addDay()->toDateString(),
                'check_out_date' => now()->addDays(2)->toDateString(),
            ]).'#rooms');
    }
}
