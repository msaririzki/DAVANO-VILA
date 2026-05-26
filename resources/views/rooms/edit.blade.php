<x-app-layout>
    <x-slot name="header">
        <div class="relative">
            <div class="absolute -left-4 top-1/2 h-8 w-1 -translate-y-1/2 rounded-full bg-emerald-600"></div>
            <div class="flex items-center gap-1.5 text-xs font-black uppercase tracking-[0.2em] text-emerald-700">
                <a href="{{ route('rooms.index') }}" class="transition-colors hover:text-emerald-950 font-black">Master Kamar</a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                <span>Edit Kamar</span>
            </div>
            <h2 class="mt-1 text-2xl font-black tracking-tight text-neutral-950">Ubah Rincian Unit</h2>
            <p class="mt-1 text-sm font-medium text-neutral-500">Sesuaikan deskripsi, harga sewa, fasilitas, status operasional, serta gambar dari kamar.</p>
        </div>
    </x-slot>

    <div class="relative min-h-screen bg-[radial-gradient(circle_at_top_right,rgba(16,185,129,0.06),transparent_40rem)] py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('rooms.update', $room) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')
                @include('rooms._form', ['submitLabel' => 'Simpan Perubahan Unit'])
            </form>
        </div>
    </div>
</x-app-layout>

