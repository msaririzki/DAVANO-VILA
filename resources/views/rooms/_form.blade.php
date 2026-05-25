@php
    use App\Models\Room;

    $facilitiesText = old('facilities_text', implode(PHP_EOL, $room->facilities ?? []));
    $existingRemoteImage = str_starts_with((string) $room->image_path, 'http') ? $room->image_path : '';
@endphp

@if ($errors->any())
    <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

<div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
    <section class="rounded-lg border border-white/70 bg-white p-5 shadow-sm sm:p-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-800">Data kamar</p>
            <h3 class="mt-1 text-xl font-semibold text-gray-950">Informasi yang tampil ke tamu</h3>
        </div>

        <div class="mt-6 grid gap-4">
            <div>
                <label class="text-sm font-semibold text-gray-700">Nama kamar</label>
                <input name="name" value="{{ old('name', $room->name) }}" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-emerald-700 focus:ring-emerald-700" required>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700">Deskripsi singkat</label>
                <textarea name="description" rows="5" class="mt-1 w-full rounded-md border-gray-300 text-sm leading-6 focus:border-emerald-700 focus:ring-emerald-700">{{ old('description', $room->description) }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Harga per malam</label>
                    <input name="price" type="number" min="0" step="1" value="{{ old('price', (int) $room->price) }}" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-emerald-700 focus:ring-emerald-700" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700">Kapasitas tamu</label>
                    <input name="capacity" type="number" min="1" max="50" value="{{ old('capacity', $room->capacity) }}" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-emerald-700 focus:ring-emerald-700" required>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Status operasional</label>
                    <select name="status" class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-emerald-700 focus:ring-emerald-700" required>
                        <option value="{{ Room::STATUS_AVAILABLE }}" @selected(old('status', $room->status) === Room::STATUS_AVAILABLE)>Available</option>
                        <option value="{{ Room::STATUS_CLEANING }}" @selected(old('status', $room->status) === Room::STATUS_CLEANING)>Cleaning</option>
                        <option value="{{ Room::STATUS_MAINTENANCE }}" @selected(old('status', $room->status) === Room::STATUS_MAINTENANCE)>Maintenance</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex min-h-[42px] w-full items-center justify-between rounded-md border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-700">
                        Tampilkan di publik
                        <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $room->exists ? $room->is_active : true)) class="rounded border-gray-300 text-emerald-700 focus:ring-emerald-700">
                    </label>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-lg border border-white/70 bg-white p-5 shadow-sm sm:p-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-amber-700">Foto dan fasilitas</p>
            <h3 class="mt-1 text-xl font-semibold text-gray-950">Materi publik kamar</h3>
        </div>

        <div class="mt-6 grid gap-4">
            @if ($room->imageUrl())
                <img src="{{ $room->imageUrl() }}" alt="{{ $room->name }}" class="aspect-[2.4/1] w-full rounded-md bg-gray-100 object-contain">
            @else
                <div class="flex aspect-[2.4/1] items-center justify-center rounded-md border border-dashed border-gray-300 bg-gray-50 text-sm font-medium text-gray-500">
                    Belum ada foto kamar
                </div>
            @endif

            <div>
                <label class="text-sm font-semibold text-gray-700">Upload foto</label>
                <input name="image" type="file" accept="image/*" class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-gray-950 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-emerald-800">
                <p class="mt-1 text-xs leading-5 text-gray-500">Gunakan foto horizontal. Maksimal 4 MB.</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700">Atau URL foto</label>
                <input name="image_url" type="url" value="{{ old('image_url', $existingRemoteImage) }}" placeholder="https://..." class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700">Fasilitas kamar</label>
                <textarea name="facilities_text" rows="7" placeholder="King bed&#10;Private bathroom&#10;Hot water&#10;Wi-Fi" class="mt-1 w-full rounded-md border-gray-300 text-sm leading-6 focus:border-emerald-700 focus:ring-emerald-700">{{ $facilitiesText }}</textarea>
                <p class="mt-1 text-xs leading-5 text-gray-500">Satu fasilitas per baris, atau pisahkan dengan koma.</p>
            </div>
        </div>
    </section>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('rooms.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-800 hover:border-gray-950">
        Batal
    </a>
    <button class="inline-flex justify-center rounded-md bg-gray-950 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-800">
        {{ $submitLabel }}
    </button>
</div>
