# DAVANO-VILA

Sistem informasi pemesanan dan manajemen operasional villa dengan prinsip 100% cashless dan validasi pembayaran terpusat oleh Super Admin.

## Stack

- Laravel 13.11
- PHP 8.4
- SQLite untuk development lokal
- MySQL untuk deployment produksi
- Blade, Tailwind CSS, Alpine.js, Vite

## Setup Lokal

```bash
composer install
npm install
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Aplikasi lokal berjalan di:

```text
http://127.0.0.1:8000
```

## Akun Awal

Super Admin:

```text
email: superadmin@dafanovilla.test
password: password
```

Admin:

```text
email: admin@dafanovilla.test
password: password
```

## Fitur yang Sudah Ada

- PRD final kerja di `PRD_FINAL_DAFANO_VILLA.md`.
- Auth login Blade.
- Registrasi publik dimatikan.
- Role `super_admin` dan `admin`.
- Middleware `role:super_admin`.
- Migration utama: rooms, bank accounts, bookings, booking add-ons, payments, settings.
- Seeder user awal, kamar awal, rekening awal, dan setting awal.
- Halaman publik untuk cari kamar, buat booking pending, dan konfirmasi WhatsApp.
- Halaman publik multi bahasa: Indonesia, English, Melayu, Mandarin, dan Japanese.
- Deteksi bahasa otomatis dari `Accept-Language` browser dengan pilihan manual di halaman publik.
- Logika availability: hanya booking `DP` atau `Lunas` yang mengunci kamar.
- Dashboard internal dengan ringkasan booking.
- Validasi pembayaran booking hanya oleh Super Admin.
- Update status operasional booking oleh staff login.

## Verifikasi

```bash
php artisan test
npm run build
vendor/bin/pint
```

Status terakhir: 28 test pass.

## Fase Berikutnya

1. Add-ons/running tab: makanan dan extra bed.
2. Diskon manual Super Admin dan late checkout fee.
3. Checkout completed, kamar masuk status cleaning, tombol kamar siap.
4. Kwitansi PDF.
5. Telegram notification dan Telegram auto-report.
6. Laporan dashboard, chart sumber referensi, export laporan.
7. Panduan deployment cPanel.
