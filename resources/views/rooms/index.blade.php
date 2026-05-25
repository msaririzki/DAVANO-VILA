<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-950">Dashboard</a>
                <h2 class="mt-1 text-2xl font-semibold text-gray-950">Master kamar</h2>
            </div>
            <a href="{{ route('rooms.create') }}" class="inline-flex justify-center rounded-md bg-gray-950 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                Tambah kamar
            </a>
        </div>
    </x-slot>

    <div class="bg-[#f6f4ef] py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($rooms as $room)
                    <article class="flex h-full flex-col overflow-hidden rounded-lg border border-white/70 bg-white shadow-sm">
                        @if ($room->imageUrl())
                            <img src="{{ $room->imageUrl() }}" alt="{{ $room->name }}" class="aspect-[2.4/1] w-full bg-gray-100 object-contain">
                        @else
                            <div class="flex aspect-[2.4/1] items-center justify-center bg-gray-100 text-sm font-medium text-gray-500">
                                Belum ada foto
                            </div>
                        @endif

                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-950">{{ $room->name }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">Rp {{ number_format($room->price, 0, ',', '.') }} / malam</p>
                                </div>
                                <span class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold {{ $room->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $room->is_active ? 'Publik' : 'Hidden' }}
                                </span>
                            </div>

                            <p class="mt-4 line-clamp-3 text-sm leading-6 text-gray-600 lg:min-h-[4.5rem]">{{ $room->description ?: 'Belum ada deskripsi.' }}</p>

                            <div class="mt-4 flex flex-wrap gap-2 lg:min-h-[7rem]">
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ $room->capacity }} tamu</span>
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">{{ $room->status }}</span>
                                @foreach ($room->facilities ?? [] as $facility)
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $facility }}</span>
                                @endforeach
                            </div>

                            <a href="{{ route('rooms.edit', $room) }}" class="mt-auto inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 hover:border-gray-950">
                                Edit kamar
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-white/70 bg-white p-8 text-center text-sm text-gray-500 md:col-span-2 xl:col-span-3">
                        Belum ada kamar. Tambahkan kamar pertama untuk mulai menerima reservasi.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
