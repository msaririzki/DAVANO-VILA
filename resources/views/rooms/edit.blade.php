<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-black text-slate-800 tracking-tight">Edit kamar</h2>
                        <p class="mt-0.5 text-xs font-semibold text-slate-500">Ubah Rincian Unit</p>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">Ubah</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all font-bold text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Batal
            </a>
        </div>
    </x-slot>

    <div class="relative min-h-screen bg-slate-50 pt-6 pb-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('rooms.update', $room) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')
                @include('rooms._form', ['submitLabel' => 'Simpan Perubahan Unit'])
            </form>
        </div>
    </div>
</x-app-layout>

