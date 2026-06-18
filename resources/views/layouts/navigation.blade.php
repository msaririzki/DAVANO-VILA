<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-white/70 bg-white/90 shadow-[0_18px_55px_-48px_rgba(15,23,42,0.8)] backdrop-blur-xl">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-20 items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-4">
                <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <x-application-logo class="block h-14 w-12 shrink-0 rounded-2xl bg-white p-1 object-contain shadow-sm ring-1 ring-neutral-200" />
                    <div class="hidden min-w-0 sm:block">
                        <p class="truncate text-sm font-black text-neutral-950">Villa Dafano</p>
                        <p class="truncate text-xs font-semibold text-neutral-500">Sistem Operasional</p>
                    </div>
                </a>

                <div class="hidden items-center gap-1 lg:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Operasional
                    </x-nav-link>
                    <x-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
                        Buat Pemesanan
                    </x-nav-link>
                    @if (Auth::user()->isSuperAdmin())
                        <x-nav-link :href="route('admin.web-settings')" :active="request()->routeIs('admin.web-settings') || request()->routeIs('rooms.*') || request()->routeIs('addon-items.*')">
                            Pengaturan Web
                        </x-nav-link>
                        <x-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                            Laporan
                        </x-nav-link>
                        <x-nav-link :href="route('admin.audit-logs')" :active="request()->routeIs('admin.audit-logs')">
                            Log Aktivitas
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden items-center gap-3 lg:flex">
                <a href="{{ route('public.home') }}" class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-4 py-2 text-sm font-bold text-neutral-700 shadow-sm transition hover:border-emerald-200 hover:text-emerald-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                    Halaman publik
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 rounded-full border border-neutral-200 bg-neutral-950 py-1.5 pl-1.5 pr-4 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-sm font-black text-neutral-950">
                                {{ str(Auth::user()->name)->substr(0, 1)->upper() }}
                            </span>
                            <span class="max-w-40 truncate">{{ Auth::user()->name }}</span>
                            <span class="rounded-full bg-white/10 px-2 py-0.5 text-[0.65rem] font-black uppercase tracking-wide">{{ Auth::user()->isSuperAdmin() ? 'CEO' : 'Admin' }}</span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil Saya
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center gap-2 lg:hidden">
                <span class="rounded-full bg-neutral-950 px-3 py-1 text-xs font-black uppercase tracking-wide text-white">{{ Auth::user()->isSuperAdmin() ? 'CEO' : 'Admin' }}</span>
                <button @click="open = ! open" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-neutral-200 bg-white text-neutral-600 shadow-sm transition hover:text-neutral-950">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-neutral-100 bg-white/95 px-4 pb-5 pt-3 lg:hidden">
        <div class="space-y-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Operasional
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
                Buat Pemesanan
            </x-responsive-nav-link>
            @if (Auth::user()->isSuperAdmin())
                <x-responsive-nav-link :href="route('admin.web-settings')" :active="request()->routeIs('admin.web-settings') || request()->routeIs('rooms.*') || request()->routeIs('addon-items.*')">
                    Pengaturan Web
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                    Laporan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.audit-logs')" :active="request()->routeIs('admin.audit-logs')">
                    Log Aktivitas
                </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('public.home')" :active="false">
                Halaman publik
            </x-responsive-nav-link>
        </div>

        <div class="mt-4 rounded-2xl bg-neutral-50 p-4">
            <div class="font-black text-neutral-950">{{ Auth::user()->name }}</div>
            <div class="mt-1 text-sm font-semibold text-neutral-500">{{ Auth::user()->email }}</div>
            <div class="mt-3 grid gap-2">
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    Profil Saya
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
