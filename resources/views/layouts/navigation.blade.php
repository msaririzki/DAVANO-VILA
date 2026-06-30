@php
    $menuItems = [
        [
            'label' => 'Operasional',
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25m-4.5-13.5h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0L6.75 21M9 11.25v1.5M12 9v3.75m3-6v6" />',
        ],
        [
            'label' => 'Buat Pesanan',
            'route' => 'bookings.create',
            'active' => request()->routeIs('bookings.create'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />',
        ],
    ];

    if (Auth::user()->isSuperAdmin()) {
        $menuItems = array_merge($menuItems, [
            [
                'label' => 'Pengaturan Web',
                'route' => 'admin.web-settings',
                'active' => request()->routeIs('admin.web-settings') || request()->routeIs('rooms.*') || request()->routeIs('addon-items.*'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a6.8 6.8 0 0 1 0 .255c-.008.378.137.75.43.992l1.003.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.5 6.5 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.212-1.281c-.063-.374-.313-.686-.645-.87a6.5 6.5 0 0 1-.22-.127c-.324-.196-.72-.257-1.075-.124l-1.217.456a1.125 1.125 0 0 1-1.37-.49l-1.296-2.247a1.125 1.125 0 0 1 .26-1.431l1.003-.827c.293-.242.438-.614.43-.992a6.8 6.8 0 0 1 0-.255c.008-.379-.137-.751-.43-.992l-1.003-.827a1.125 1.125 0 0 1-.26-1.43l1.298-2.247a1.125 1.125 0 0 1 1.369-.491l1.217.456c.355.133.75.072 1.076-.124.072-.044.146-.086.22-.128.331-.183.581-.495.644-.869l.213-1.281Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />',
            ],
            [
                'label' => 'Laporan',
                'route' => 'admin.reports',
                'active' => request()->routeIs('admin.reports'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />',
            ],
            [
                'label' => 'Log Aktivitas',
                'route' => 'admin.audit-logs',
                'active' => request()->routeIs('admin.audit-logs'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
            ],
        ]);
    }
@endphp

<div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/45 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" aria-hidden="true"></div>

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200/80 bg-white shadow-2xl shadow-slate-950/10 transition-transform duration-200 ease-out lg:translate-x-0 lg:shadow-none"
>
    <div class="flex h-20 shrink-0 items-center justify-between border-b border-slate-100 px-5">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
            <x-application-logo class="h-12 w-12 shrink-0 rounded-2xl bg-white p-1 object-contain shadow-sm ring-1 ring-slate-200" />
            <div class="min-w-0">
                <p class="truncate text-base font-black tracking-tight text-slate-950">Villa Dafano</p>
                <p class="truncate text-xs font-semibold text-slate-500">Sistem Operasional</p>
            </div>
        </a>
        <button type="button" @click="sidebarOpen = false" class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-900 lg:hidden" aria-label="Tutup menu">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto px-4 py-6" aria-label="Menu utama">
        <p class="mb-3 px-3 text-[0.65rem] font-black uppercase tracking-[0.18em] text-slate-400">Menu Utama</p>
        <div class="space-y-1.5">
            @foreach ($menuItems as $item)
                <a href="{{ route($item['route']) }}" @click="sidebarOpen = false" @class([
                    'group flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold transition',
                    'bg-emerald-700 text-white shadow-md shadow-emerald-800/15' => $item['active'],
                    'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800' => ! $item['active'],
                ])>
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">{!! $item['icon'] !!}</svg>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach

            <a href="{{ route('public.home') }}" target="_blank" rel="noopener" class="group flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-600 transition hover:bg-emerald-50 hover:text-emerald-800">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 3L21 3m0 0h-5.25M21 3v5.25" /></svg>
                <span>Halaman Publik</span>
                <svg class="ml-auto h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-6 0L21 3m0 0h-5.25M21 3v5.25" /></svg>
            </a>
        </div>
    </nav>

    <div class="shrink-0 border-t border-slate-100 p-4">
        <a href="{{ route('profile.edit') }}" class="mb-3 flex items-center gap-3 rounded-2xl bg-slate-50 p-3 transition hover:bg-slate-100">
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-950 text-sm font-black text-white">
                {{ str(Auth::user()->name)->substr(0, 1)->upper() }}
            </span>
            <span class="min-w-0 flex-1">
                <span class="block truncate text-sm font-black text-slate-900">{{ Auth::user()->name }}</span>
                <span class="block truncate text-xs font-semibold text-slate-500">{{ Auth::user()->isSuperAdmin() ? 'CEO' : 'Admin' }}</span>
            </span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-black text-red-700 transition hover:border-red-300 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500/30">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

<div class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200/80 bg-white/95 px-4 backdrop-blur lg:hidden">
    <button type="button" @click="sidebarOpen = true" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm" aria-label="Buka menu" :aria-expanded="sidebarOpen">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
    </button>
    <div class="text-center">
        <p class="text-sm font-black text-slate-900">Villa Dafano</p>
        <p class="text-[0.65rem] font-bold uppercase tracking-wider text-emerald-700">{{ Auth::user()->isSuperAdmin() ? 'CEO' : 'Admin' }}</p>
    </div>
    <a href="{{ route('profile.edit') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-950 text-sm font-black text-white" aria-label="Buka profil">
        {{ str(Auth::user()->name)->substr(0, 1)->upper() }}
    </a>
</div>
