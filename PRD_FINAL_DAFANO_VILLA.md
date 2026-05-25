# PRD Final Kerja
# Sistem Informasi Pemesanan dan Manajemen Villa Dafano

## 1. Prinsip Utama

Sistem dibangun untuk mencegah kebocoran uang operasional dengan pola 100% cashless dan validasi pembayaran terpusat.

Aturan wajib:

1. Tidak ada fitur pembayaran tunai.
2. Semua pembayaran dilakukan melalui transfer bank ke rekening perusahaan.
3. Hanya Super Admin yang boleh mengubah nilai uang dan status pembayaran.
4. Harga di sistem adalah harga net, tanpa kalkulasi PPN atau service charge.
5. Tidak ada voucher atau kode promo publik.
6. Diskon hanya bisa diberikan manual oleh Super Admin pada detail booking.
7. Jam check-in standar 14:00 dan check-out standar 12:00.

## 2. Role dan Batas Akses

### Super Admin

Maksimal 2 akun.

Hak akses:

- Validasi pembayaran booking, pelunasan, dan add-ons.
- Mengubah `paid_amount`, `payment_status`, `discount_amount`, dan catatan finansial.
- Mengelola master data kamar, harga, menu/add-ons, rekening bank, dan settings.
- Melihat dashboard analitik dan laporan.
- Melakukan cancel booking jika diperlukan.

### Admin

Maksimal 2 akun.

Hak akses:

- Melihat jadwal tamu, status pembayaran, dan sisa tagihan.
- Mengubah status operasional booking: `Booked`, `In-House`, `Completed`, `No-Show`.
- Mengubah status kamar: `Cleaning`, `Available`.
- Menginput add-ons makanan atau extra bed ke running tab.
- Mencetak kwitansi.

Larangan:

- Tidak boleh mengubah harga kamar.
- Tidak boleh mengubah diskon.
- Tidak boleh mengubah nominal pembayaran.
- Tidak boleh memvalidasi pembayaran.
- Tidak boleh menghapus transaksi finansial.

## 3. Keputusan Status Final

### `payment_status` pada `bookings`

- `Pending`: booking dibuat, pembayaran belum divalidasi.
- `DP`: pembayaran awal sudah divalidasi.
- `Lunas`: semua tagihan sudah lunas.
- `Cancelled`: booking dibatalkan.

### `booking_status` pada `bookings`

- `Booked`: booking tercatat.
- `In-House`: tamu sedang menginap.
- `Completed`: tamu sudah checkout dan transaksi selesai.
- `No-Show`: tamu tidak datang.
- `Cancelled`: booking dibatalkan.

Booking baru dibuat dengan:

- `payment_status = Pending`
- `booking_status = Booked`

### `status` pada `rooms`

- `Available`: kamar siap dijual.
- `Cleaning`: kamar selesai dipakai dan sedang/menunggu dibersihkan.
- `Maintenance`: kamar tidak dijual karena perbaikan.

### `payment_status` pada `booking_addons`

- `Pending`: add-on sudah diinput, pembayaran belum divalidasi.
- `Paid`: add-on sudah dibayar dan boleh diproses.
- `Cancelled`: add-on dibatalkan.

## 4. Aturan Ketersediaan Kamar

Booking dengan `payment_status = Pending` tidak mengunci kamar.

Kamar dianggap tidak tersedia jika:

- `rooms.status != Available`, atau
- ada booking aktif pada kamar tersebut dengan `payment_status` `DP` atau `Lunas`, dan tanggalnya overlap dengan tanggal pencarian.

Rumus overlap:

```sql
check_in_date < requested_check_out
AND check_out_date > requested_check_in
```

Konsekuensi:

- Bisa ada lebih dari satu booking `Pending` untuk kamar dan tanggal yang sama.
- Yang pertama divalidasi DP/Lunas oleh Super Admin menjadi booking aktif.
- Booking pending lain pada slot yang sama harus diberi tanda konflik atau dibatalkan manual oleh Super Admin.

## 5. Alur Booking Publik

1. Tamu memilih tanggal check-in dan check-out.
2. Sistem menampilkan kamar yang tersedia.
3. Tamu memilih kamar dan mengisi:
   - nama,
   - nomor WhatsApp,
   - sumber mengetahui villa: Instagram, Google, Teman, TikTok, Walk-in, Lainnya.
4. Sistem menghitung:
   - total harga kamar,
   - minimal DP berdasarkan setting,
   - grand total,
   - sisa tagihan.
5. Tamu melihat daftar rekening bank aktif.
6. Tamu klik tombol konfirmasi WhatsApp.
7. Sistem membuat booking `Pending` dan mengirim notifikasi Telegram ke Super Admin.
8. Tamu mengirim bukti transfer melalui WhatsApp.

## 6. Alur Validasi Booking

1. Super Admin menerima notifikasi Telegram.
2. Super Admin cek mutasi bank secara manual.
3. Jika uang masuk, Super Admin membuka detail booking.
4. Super Admin menginput nominal pembayaran dan memilih status:
   - `DP`, atau
   - `Lunas`.
5. Sistem mencatat pembayaran di tabel `payments`.
6. Sistem menghitung ulang `paid_amount`, `balance_due`, dan `payment_status`.

## 7. Alur Menginap dan Add-ons

1. Saat tamu datang, Admin mengubah `booking_status` menjadi `In-House`.
2. Jika tamu pesan makanan atau extra bed, Admin input add-on ke running tab.
3. Add-on langsung menambah tagihan booking, tetapi status add-on tetap `Pending`.
4. Tamu wajib transfer biaya add-on.
5. Super Admin cek mutasi dan memvalidasi pembayaran add-on.
6. Setelah add-on `Paid`, dapur/pegawai boleh memproses pesanan.

## 8. Alur Checkout

1. Admin membuka detail booking dan melihat sisa tagihan.
2. Jika ada nego, hanya Super Admin yang boleh mengisi:
   - `discount_amount`,
   - `discount_note`.
3. Diskon tidak boleh membuat `balance_due` menjadi negatif.
4. Jika ada late checkout atau biaya tambahan manual, Super Admin menambahkan `late_fee`.
5. Tamu transfer sisa pembayaran.
6. Super Admin validasi pembayaran hingga `payment_status = Lunas`.
7. Admin mencetak kwitansi PDF.
8. Admin mengubah `booking_status = Completed`.
9. Sistem mengubah `rooms.status = Cleaning`.
10. Setelah kamar selesai dibersihkan, Admin menekan "Kamar Siap" sehingga `rooms.status = Available`.

## 9. Cancel, No-Show, dan Refund

Aturan awal:

- Booking `Pending` boleh dibatalkan oleh Super Admin.
- Booking `DP` atau `Lunas` boleh dibatalkan hanya oleh Super Admin.
- Sistem tidak melakukan refund otomatis.
- Jika ada refund, dicatat manual sebagai catatan pembayaran/refund di `payments`.
- Booking yang dibatalkan memakai `payment_status = Cancelled` dan `booking_status = Cancelled`.
- `No-Show` hanya mengubah `booking_status`, status pembayaran tetap sesuai kondisi terakhir.

## 10. Bukti Transfer

Versi awal tidak menyediakan upload bukti transfer di website.

Bukti transfer dikirim melalui WhatsApp. Sistem menyimpan:

- catatan validasi pembayaran,
- rekening tujuan,
- nominal,
- waktu validasi,
- Super Admin yang memvalidasi.

Fitur upload bukti transfer dapat ditambahkan pada fase lanjutan jika dibutuhkan.

## 11. Telegram

Notifikasi langsung:

- booking baru,
- add-on baru,
- pembayaran butuh validasi.

Implementasi:

- event aplikasi mengirim job ke queue untuk Telegram.
- auto-report harian/mingguan/bulanan menggunakan Laravel scheduler dan cron cPanel.

## 12. Database Utama

Gunakan foreign key dan tipe `decimal(15,2)` untuk uang.

### `users`

- id
- name
- email
- password
- role: `super_admin`, `admin`

### `rooms`

- id
- name
- description
- price
- capacity
- status: `Available`, `Cleaning`, `Maintenance`
- image_path
- is_active

### `bank_accounts`

- id
- bank_name
- account_number
- account_name
- is_active

### `bookings`

- id
- booking_code
- guest_name
- guest_phone
- acquisition_source
- room_id
- check_in_date
- check_out_date
- total_room_price
- total_addons_price
- late_fee
- discount_amount
- discount_note
- grand_total
- paid_amount
- balance_due
- payment_status
- booking_status
- cancelled_at
- cancellation_note

### `booking_addons`

- id
- booking_id
- item_name
- type: `food`, `extrabed`
- qty
- price
- subtotal
- payment_status

### `payments`

- id
- booking_id
- booking_addon_id nullable
- type: `booking_dp`, `booking_lunas`, `addon`, `refund`, `adjustment`
- amount
- bank_account_id nullable
- validated_by
- validated_at
- note

### `settings`

- id
- key_name
- value

Minimal setting:

- `min_dp_percent = 50`
- `villa_whatsapp_number`
- `telegram_bot_token`
- `telegram_chat_id`

## 13. Fase Pengerjaan

### Fase 1: Fondasi Backend

- Scaffold Laravel.
- Setup database.
- Auth login.
- Role middleware.
- Migration dan model utama.
- Seeder user awal, kamar, rekening, dan settings.

### Fase 2: Booking Publik

- Halaman cari kamar.
- Logika overlap availability.
- Form booking.
- Perhitungan harga, DP, dan balance.
- Tombol konfirmasi WhatsApp.
- Notifikasi Telegram booking baru.

### Fase 3: Dashboard Internal

- Dashboard Super Admin dan Admin.
- Daftar booking.
- Detail booking.
- Validasi DP/Lunas oleh Super Admin.
- Update status tamu oleh Admin.
- Update status kamar.

### Fase 4: Add-ons dan Checkout

- Running tab add-ons.
- Validasi pembayaran add-ons.
- Diskon manual Super Admin.
- Late fee.
- Checkout completed.
- Kwitansi PDF.

### Fase 5: Laporan dan Operasional Lanjutan

- Pendapatan bulan ini.
- Sisa tagihan belum lunas.
- Okupansi.
- Chart sumber referensi tamu.
- Export laporan.
- Telegram auto-report.
- Backup database via scheduler.

## 14. Tech Stack

- Laravel 13 jika tersedia stabil di Composer.
- Jika Laravel 13 belum tersedia stabil, gunakan versi Laravel stabil terbaru yang tersedia di Composer dan catat keputusan di README.
- MySQL.
- Blade, Tailwind CSS, Alpine.js.
- cPanel/shared hosting deployment, dengan core Laravel di luar `public_html`.
