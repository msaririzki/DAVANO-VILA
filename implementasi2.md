# Product Requirements Document (PRD)
# Sistem Informasi Pemesanan dan Manajemen Villa

## 1. Ringkasan Eksekutif (Executive Summary)
Sistem Informasi Pemesanan Villa berbasis web yang dirancang untuk mengatasi masalah operasional dan mencegah kebocoran dana akibat penyalahgunaan uang tunai oleh pegawai. Filosofi utama aplikasi ini adalah **100% Cashless dan Terpusat**. Semua transaksi keuangan mengalir langsung ke rekening perusahaan, dan otoritas validasi keuangan hanya dipegang oleh level pimpinan (Super Admin). Aplikasi ini melayani dua antarmuka: Web Publik untuk tamu dan Dashboard Manajemen untuk internal villa.

## 2. Alur Kerja Sistem (System Workflow & User Journey)
AI wajib mengikuti urutan operasional ini saat merancang *controller* dan *routing*:

### Fase 1: Pemesanan oleh Tamu (Booking Phase)
1. **Pencarian:** Tamu membuka web publik, memasukkan tanggal *Check-in* dan *Check-out*.
2. **Validasi Ketersediaan:** Sistem mengecek database. Kamar yang sudah dibooking (status DP/Lunas) pada rentang tanggal tersebut disembunyikan. Kamar yang tersedia ditampilkan.
3. **Form & Add-ons:** Tamu memilih kamar, mengisi form identitas (Nama, No HP), dan bisa menambahkan layanan *Extra Bed* atau Makanan.
4. **Checkout & Diskon:** Di halaman ringkasan, tamu bisa memasukkan Kode Voucher (jika mendapat "harga nego" dari CEO). Sistem memotong total harga jika kode valid.
5. **Instruksi Bayar:** Sistem menampilkan Total Tagihan dan Minimal DP, beserta 3 nomor rekening perusahaan (Mandiri, BNI, BCA). Status pesanan di *database*: **Pending**.

### Fase 2: Konfirmasi & Validasi Pembayaran
1. **Konfirmasi Tamu:** Tamu mengklik tombol "Konfirmasi via WA" di web, yang langsung mengarah ke nomor WA Villa dengan membawa template pesan dan Kode Booking, lalu tamu mengirimkan foto resi transfer.
2. **Notifikasi Telegram:** Bersamaan dengan itu, web men- *trigger* Telegram Bot untuk mengirim notifikasi ke grup manajemen: *"🚨 Ada pesanan/bukti transfer baru dari [Nama Tamu]. Silakan cek mutasi."*
3. **Pengecekan Admin (Read-Only):** Admin pegawai melihat di *dashboard* ada pesanan masuk berstatus "Pending", tapi tidak bisa mengubahnya.
4. **Validasi Super Admin:** Ibu Pratiwi/Adik Ipar (Super Admin) mengecek mutasi bank di HP mereka. Jika uang masuk, mereka membuka *dashboard* dan mengklik tombol **"Terima Pembayaran"** (memilih status DP atau Lunas).
5. **Status Update:** Status pesanan di database berubah. Jadwal kamar resmi terkunci.

### Fase 3: Kedatangan & Pesanan Tambahan (In-Stay Phase)
1. **Check-in:** Tamu datang (pukul 14:00). Admin pegawai mencari nama tamu di *dashboard* dan melihat status (misal: "DP Diterima"). Admin mengubah status *booking* menjadi **In-House** (Sedang Menginap).
2. **Pesan Makan / Layanan Malam Hari:** 
   - Tamu ingin pesan makan. Karena aturan 100% *Cashless*, tamu wajib transfer biayanya saat itu juga.
   - Tamu mengirim bukti transfer ke WA Villa.
   - Sistem Telegram otomatis memberitahu Super Admin lagi.
   - Super Admin memvalidasi pesanan tersebut dari *dashboard*, dan pesanan ditambahkan ke *Running Tab* (Tagihan Berjalan) milik kamar tersebut.
   - Dapur/Pegawai baru boleh menyiapkan pesanan setelah divalidasi Super Admin.

### Fase 4: Kepulangan (Check-out Phase)
1. **Pelunasan:** Saat jam *check-out* (pukul 12:00), tamu ke resepsionis. Jika di awal baru bayar DP, Admin melihat sisa tagihan di sistem.
2. **Transfer Sisa Tagihan:** Tamu mentransfer sisa tagihan ke rekening perusahaan. Super Admin memvalidasi pelunasan.
3. **Penyelesaian:** Setelah status berubah menjadi "Lunas", Admin pegawai mengubah status pesanan menjadi **Completed** dan kamar kembali kosong. Admin bisa mencetak kwitansi akhir (PDF/Thermal) untuk tamu.

## 3. Kebijakan Operasional & Aturan Bisnis Inti
*   **100% Cashless (No Cash System):** Tidak ada tombol/fitur penerimaan uang tunai di sistem. 
*   **Net Price:** Harga di sistem adalah harga akhir (tidak perlu *coding* kalkulasi pajak daerah/service charge saat *checkout*).
*   **Voucher Diskon:** Solusi untuk harga nego/teman. Harga master kamar statis, diskon diberikan via kode unik yang di- *generate* Super Admin.

## 4. Hak Akses (Role-Based Access Control)
*   **Super Admin (Maksimal 2 Akun):**
    *   Hanya role ini yang memiliki tombol validasi pembayaran (mengubah status menjadi Lunas/DP).
    *   Bisa menambah/mengedit master data (kamar, harga, menu, rekening, voucher).
    *   Bisa melihat seluruh laporan pendapatan.
*   **Admin Pegawai (Maksimal 2 Akun):**
    *   Bersifat **Hanya Baca (Read-Only)** terhadap urusan uang.
    *   Hanya bisa melihat daftar tamu hari ini, mengubah status *Check-in/Check-out* fisik, dan melihat (bukan mengedit) sisa tagihan.

## 5. Spesifikasi Teknis
*   **Backend Framework:** Laravel 13
*   **Database:** MySQL
*   **Frontend Template:** Blade Engine + Tailwind CSS (gunakan Alpine.js untuk interaktivitas UI).
*   **Deployment Target:** cPanel / Shared Hosting (struktur direktori aman).
*   **Desain UI/UX:** Modern, elegan, minimalis, dan 100% responsif (*Mobile-first*).

## 6. Arsitektur Database Dasar (Relasional)
Tabel minimum yang harus di- *generate* melalui *migration*:
*   **`users`**: id, name, email, password, role (super_admin, admin).
*   **`rooms`**: id, name, description, price, capacity, image_path, is_active.
*   **`bank_accounts`**: id, bank_name, account_number, account_name, is_active.
*   **`vouchers`**: id, code, discount_type (nominal/persen), discount_value, is_active.
*   **`bookings`**:
    *   id, booking_code, guest_name, guest_phone, room_id
    *   check_in_date, check_out_date
    *   total_room_price, total_addons_price, voucher_id, discount_amount
    *   grand_total, paid_amount, balance_due
    *   payment_status (Menunggu, DP, Lunas, Batal)
    *   booking_status (Pending, In-House, Completed)
*   **`booking_addons`**: id, booking_id, item_name, item_type (food/extrabed), qty, price, subtotal.
*   **`settings`**: id, key, value.

## 7. Instruksi Khusus untuk AI Developer
1.  **Proteksi Endpoint (Middleware):** Route `/payments/validate` atau apa pun yang mengubah `payment_status` dan `paid_amount` wajib dibatasi hanya untuk `role:super_admin`.
2.  **Integrasi Telegram API:** Buat *Service Class* khusus untuk *broadcast* pesan ke grup Telegram secara *asynchronous* agar tidak membuat *loading* halaman web lambat saat tamu *checkout/booking*.
3.  **Ketersediaan Kamar:** Gunakan *query WHERE BETWEEN* untuk mengecek irisan (`overlap`) tanggal pada tabel `bookings` saat memfilter kamar yang tersedia.