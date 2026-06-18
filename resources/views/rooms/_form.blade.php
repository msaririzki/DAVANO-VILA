@php
    use App\Models\Room;

    $facilitiesText = old('facilities_text', implode(PHP_EOL, $room->facilities ?? []));
    $unitsText = old('room_units_text', $room->relationLoaded('units') ? $room->units->pluck('name')->implode(PHP_EOL) : '');
    $existingRemoteImage = str_starts_with((string) $room->image_path, 'http') ? $room->image_path : '';
@endphp

@if ($errors->any())
    <div class="mb-8 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50/80 p-4 text-sm font-semibold text-red-800 backdrop-blur-sm animate-fade-in">
        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
        </span>
        <div>
            <p class="font-bold">Terjadi Kesalahan Validasi</p>
            <p class="text-xs font-semibold text-red-700/90 mt-0.5">{{ $errors->first() }}</p>
        </div>
    </div>
@endif

<div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
    <!-- KOLOM KIRI: DATA UTAMA -->
    <section class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.08)] backdrop-blur-md">
        <div class="absolute right-0 top-0 -mr-6 -mt-6 h-24 w-24 rounded-full bg-emerald-500/5 blur-xl"></div>
        
        <div>
            <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Data Kamar</p>
            <h3 class="mt-1 text-2xl font-black text-neutral-950">Spesifikasi Unit</h3>
            <p class="mt-1 text-xs font-semibold text-neutral-500">Rincian data teknis kamar yang akan ditayangkan langsung untuk publik.</p>
        </div>

        <div class="mt-6 space-y-4">
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Nama Kamar / Unit</label>
                <input name="name" value="{{ old('name', $room->name) }}" placeholder="Contoh: Deluxe Garden Villa" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold focus:border-emerald-600 focus:ring-emerald-600/20" required>
            </div>

            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Deskripsi Singkat</label>
                <textarea name="description" rows="5" placeholder="Tuliskan deskripsi keunggulan unit kamar ini..." class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold leading-relaxed focus:border-emerald-600 focus:ring-emerald-600/20">{{ old('description', $room->description) }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Harga Per Malam (Rp)</label>
                    <input name="price" type="number" min="0" step="1" value="{{ old('price', (int) $room->price) }}" placeholder="Contoh: 750000" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Kapasitas Tampil di Publik</label>
                    <input name="capacity" type="number" min="1" max="50" value="{{ old('capacity', $room->capacity) }}" placeholder="Contoh: 4" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                </div>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Aturan Kapasitas</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Kapasitas Termasuk Harga</label>
                        <input name="included_capacity" type="number" min="1" max="50" value="{{ old('included_capacity', $room->included_capacity ?: $room->capacity) }}" placeholder="Contoh: 15" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Kapasitas Maksimal</label>
                        <input name="max_capacity" type="number" min="1" max="50" value="{{ old('max_capacity', $room->max_capacity ?: $room->capacity) }}" placeholder="Contoh: 20" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-neutral-700 mb-1.5">Cara Menentukan Biaya Tamu Tambahan</label>
                        <x-custom-select
                            name="extra_guest_charge_mode"
                            :options="['manual' => 'Ditentukan manual oleh admin', 'none' => 'Tidak ada biaya otomatis']"
                            :selected="old('extra_guest_charge_mode', $room->extra_guest_charge_mode ?: 'manual')"
                            placeholder="Pilih cara penentuan biaya"
                            :required="true"
                        />
                    </div>
                    <div class="flex items-end">
                        <label class="flex min-h-[46px] w-full items-center justify-between rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-xs font-black uppercase tracking-wider text-neutral-700 cursor-pointer">
                            Tamu boleh pilih jumlah unit
                            <input name="allow_unit_quantity" type="checkbox" value="1" @checked(old('allow_unit_quantity', $room->allow_unit_quantity)) class="rounded border-neutral-300 text-emerald-600 focus:ring-emerald-500">
                        </label>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Catatan Aturan Publik</label>
                    <textarea name="capacity_rule_note" rows="3" placeholder="Contoh: Harga dasar termasuk sampai 15 orang. Jika lebih, biaya tambahan dikonfirmasi admin." class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold leading-relaxed focus:border-emerald-600 focus:ring-emerald-600/20">{{ old('capacity_rule_note', $room->capacity_rule_note) }}</textarea>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-neutral-700 mb-1.5">Status Operasional</label>
                    <x-custom-select
                        name="status"
                        :options="[
                            \App\Models\Room::STATUS_AVAILABLE => 'Tersedia dan Siap Dipesan',
                            \App\Models\Room::STATUS_CLEANING => 'Sedang Dibersihkan',
                            \App\Models\Room::STATUS_MAINTENANCE => 'Dalam Perbaikan'
                        ]"
                        :selected="old('status', $room->status)"
                        placeholder="Pilih Status Operasional"
                        :required="true"
                    />
                </div>
                <div class="flex items-end">
                    <label class="flex min-h-[46px] w-full items-center justify-between rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-neutral-700 cursor-pointer">
                        Tampilkan di Publik
                        <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $room->exists ? $room->is_active : true)) class="rounded border-neutral-300 text-emerald-600 focus:ring-emerald-500">
                    </label>
                </div>
            </div>
        </div>
    </section>

    <!-- KOLOM KANAN: MEDIA & FASILITAS -->
    <section class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.08)] backdrop-blur-md">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.16em] text-amber-700">Foto & Fasilitas</p>
            <h3 class="mt-1 text-2xl font-black text-neutral-950">Visual Halaman Tamu</h3>
            <p class="mt-1 text-xs font-semibold text-neutral-500">Berkas gambar serta daftar fasilitas pendukung kenyamanan tamu.</p>
        </div>

        <div class="mt-6 space-y-4">
            <!-- Tampilan Foto Saat Ini -->
            @if ($room->imageUrl())
                <div class="relative overflow-hidden aspect-[2.2/1] w-full rounded-2xl border border-neutral-200/80 bg-neutral-50 shadow-inner">
                    <img src="{{ $room->imageUrl() }}" alt="{{ $room->name }}" class="w-full h-full object-cover">
                </div>
            @else
                <div class="flex aspect-[2.2/1] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-neutral-200 bg-neutral-50/50 p-6 text-center text-neutral-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                    <span class="mt-2 text-xs font-black uppercase tracking-wider">Belum Ada Foto yang Diunggah</span>
                </div>
            @endif

            <!-- Unggah Berkas Baru -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Unggah Foto Baru</label>
                <div class="relative mt-1.5 rounded-xl border border-neutral-200 bg-neutral-50/50 px-3.5 py-2.5">
                    <input name="image" type="file" accept="image/*" class="block w-full text-xs font-bold text-neutral-600 file:mr-4 file:rounded-lg file:border-0 file:bg-neutral-950 file:px-3 file:py-1.5 file:text-xs file:font-black file:uppercase file:tracking-wider file:text-white hover:file:bg-emerald-700 file:cursor-pointer">
                    <p class="mt-2 text-[10px] font-semibold text-neutral-400">Gunakan berkas gambar format horizontal (Maksimal ukuran 4 MB).</p>
                </div>
            </div>

            <!-- Atau Input URL -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Atau Gunakan URL Gambar Eksternal</label>
                <input name="image_url" type="url" value="{{ old('image_url', $existingRemoteImage) }}" placeholder="https://domain.com/gambar-vila.jpg" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold focus:border-emerald-600 focus:ring-emerald-600/20">
            </div>

            <!-- Fasilitas Kamar -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Daftar Fasilitas Unit</label>
                <textarea name="facilities_text" rows="6" placeholder="Contoh:&#10;King Size Bed&#10;Kamar Mandi Air Hangat&#10;Televisi Kabel&#10;Akses Wi-Fi Cepat" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold leading-relaxed focus:border-emerald-600 focus:ring-emerald-600/20">{{ $facilitiesText }}</textarea>
                <p class="mt-1.5 text-[10px] font-semibold text-neutral-400">Tuliskan satu jenis fasilitas per baris (tekan Enter untuk baris baru).</p>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-neutral-50/70 p-4">
                <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Unit Fisik</label>
                <textarea name="room_units_text" rows="7" placeholder="Contoh:&#10;Commercial Villa 01&#10;Commercial Villa 02&#10;Commercial Villa 03" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold leading-relaxed focus:border-emerald-600 focus:ring-emerald-600/20">{{ $unitsText }}</textarea>
                <p class="mt-1.5 text-[10px] font-semibold text-neutral-400">Tamu tidak memilih nomor unit tertentu. Admin menetapkan unit melalui halaman detail pemesanan.</p>
            </div>
        </div>
    </section>
</div>

<!-- TOMBOL SUBMIT FORMS -->
<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-neutral-200/50 pt-6">
    <a href="{{ route('rooms.index') }}" class="inline-flex justify-center items-center gap-1.5 rounded-2xl border border-neutral-200 bg-white px-6 py-3.5 text-sm font-bold text-neutral-800 shadow-sm transition-all hover:border-neutral-950 hover:bg-neutral-50 active:scale-95">
        Batal
    </a>
    <button type="submit" class="inline-flex justify-center items-center gap-1.5 rounded-2xl bg-emerald-700 px-6 py-3.5 text-sm font-black text-white shadow-xl shadow-emerald-700/20 transition-all hover:bg-emerald-800 hover:-translate-y-0.5 active:scale-[0.98]">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4.13-5.69Z" clip-rule="evenodd" /></svg>
        {{ $submitLabel }}
    </button>
</div>
