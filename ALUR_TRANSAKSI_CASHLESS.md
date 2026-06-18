# Alur Transaksi Cashless Dafano Villa

## Pembagian tugas

### Admin

- Membuat booking internal.
- Melihat jadwal dan sisa tagihan.
- Menambahkan atau membatalkan layanan yang belum dibayar.
- Melakukan check-in, check-out, dan menandai unit selesai dibersihkan.
- Tidak dapat mencatat pembayaran, diskon, refund, atau mengubah rekening.

### Super Admin

- Memeriksa mutasi rekening perusahaan.
- Memvalidasi transfer DP, pelunasan, dan add-on.
- Memberikan diskon atau biaya tambahan dengan catatan.
- Membatalkan booking dan mencatat refund.
- Mengelola rekening perusahaan dan melihat laporan keuangan.

## Alur booking

1. Tamu atau Admin membuat booking.
2. Booking berstatus `Pending` dan belum mengunci unit.
3. Tamu transfer minimal DP ke rekening perusahaan.
4. Super Admin mencocokkan mutasi, lalu mengisi:
   - nominal transfer;
   - rekening penerima;
   - referensi transfer/mutasi yang unik.
5. Sistem memeriksa ulang stok kamar.
6. Jika stok tersedia dan DP terpenuhi, status menjadi `DP` atau `Lunas` dan kamar terkunci.
7. Jika slot sudah digunakan booking lain, transfer tidak dicatat ke sistem sampai booking dipindahkan atau dibatalkan.

## Alur menginap

1. Admin menekan `Proses Check-in`.
2. Sistem memastikan minimal DP sudah diterima.
3. Sistem memilih unit fisik yang tersedia.
4. Admin dapat menambahkan makanan, minuman, extra bed, atau layanan lain.
5. Semua layanan otomatis menambah total dan sisa tagihan.
6. Super Admin memvalidasi transfer tambahan melalui form transfer yang sama.

## Alur check-out

1. Sistem menampilkan sisa tagihan akhir.
2. Tamu melakukan transfer pelunasan.
3. Super Admin memvalidasi transfer sampai status `Lunas`.
4. Admin menekan `Proses Check-out`.
5. Unit otomatis menjadi `Cleaning`.
6. Setelah selesai dibersihkan, Admin menekan `Unit Sudah Siap`.
7. Unit kembali menjadi `Available`.

## Pembatalan dan refund

1. Hanya Super Admin yang dapat membatalkan booking.
2. Alasan pembatalan wajib diisi.
3. Refund hanya dicatat jika transfer pengembalian benar-benar sudah dilakukan.
4. Refund wajib memiliki rekening pengirim dan referensi transfer unik.
5. Refund mengurangi pendapatan bersih dan uang bersih yang tercatat pada booking.
6. Booking yang dibatalkan melepaskan unit dan tidak memiliki sisa tagihan.

## Aturan keuangan

- Tidak ada transaksi tunai.
- Pembayaran tanpa rekening aktif ditolak.
- Referensi transfer yang sama tidak dapat digunakan dua kali.
- Transfer pertama yang tidak mencapai minimal DP ditolak.
- Pembayaran tidak boleh melebihi sisa tagihan.
- Diskon atau pembatalan add-on tidak boleh membuat total tagihan lebih kecil daripada uang yang sudah diterima.
- Booking selesai hanya jika sudah `Lunas`.
- Semua perubahan penting masuk audit log.
