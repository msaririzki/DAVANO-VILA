<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-950">Dashboard</a>
            <h2 class="mt-1 text-2xl font-semibold text-gray-950">Master Add-ons</h2>
        </div>
    </x-slot>

    <div class="bg-[#f6f4ef] py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[0.85fr_1.15fr] lg:px-8">
            @if (session('status'))
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 lg:col-span-2">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 lg:col-span-2">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="rounded-lg border border-white/70 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-950">Tambah menu</h3>
                <form method="POST" action="{{ route('addon-items.store') }}" class="mt-5 grid gap-4">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Nama</label>
                        <input name="name" class="mt-1 w-full rounded-md border-gray-300 text-sm" required>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Tipe</label>
                        <select name="type" class="mt-1 w-full rounded-md border-gray-300 text-sm" required>
                            <option value="food">Makanan/minuman</option>
                            <option value="extrabed">Extra bed</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Harga</label>
                        <input name="price" type="number" min="0" step="1" class="mt-1 w-full rounded-md border-gray-300 text-sm" required>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input name="is_active" type="checkbox" value="1" checked class="rounded border-gray-300 text-emerald-700 focus:ring-emerald-700">
                        Aktif
                    </label>
                    <button class="rounded-md bg-gray-950 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Tambah add-on</button>
                </form>
            </section>

            <section class="overflow-hidden rounded-lg border border-white/70 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h3 class="text-lg font-semibold text-gray-950">Daftar add-ons</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach ($addonItems as $addonItem)
                        <form method="POST" action="{{ route('addon-items.update', $addonItem) }}" class="grid gap-3 p-5 md:grid-cols-[1fr_150px_150px_90px_auto]">
                            @csrf
                            @method('PATCH')
                            <input name="name" value="{{ $addonItem->name }}" class="rounded-md border-gray-300 text-sm" required>
                            <select name="type" class="rounded-md border-gray-300 text-sm">
                                <option value="food" @selected($addonItem->type === 'food')>Makanan</option>
                                <option value="extrabed" @selected($addonItem->type === 'extrabed')>Extra bed</option>
                            </select>
                            <input name="price" type="number" min="0" step="1" value="{{ (int) $addonItem->price }}" class="rounded-md border-gray-300 text-sm" required>
                            <label class="inline-flex items-center justify-center gap-2 text-sm text-gray-700">
                                <input name="is_active" type="checkbox" value="1" @checked($addonItem->is_active) class="rounded border-gray-300 text-emerald-700 focus:ring-emerald-700">
                                Aktif
                            </label>
                            <button class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:border-gray-950">Simpan</button>
                        </form>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
