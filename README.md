<div align="center">

# Aplikasi Transportasi

<p align="center">
   <img src="LogUnsulbar.png" alt="Logo" width="300"/>
</p>

<h4>Framework Web Based · 2025</h4>

</div>

## Ringkasan
Aplikasi ini membantu admin, sopir, dan penumpang mengelola pemesanan perjalanan antar kota. Sistem mendukung pengelolaan rute, jadwal sopir, serta pemesanan dan riwayat perjalanan penumpang.

## Peran dan Fitur Utama
### Admin
- Kelola pengguna (CRUD User).
- Kelola rute dan jadwal sopir.
- Memantau pesanan terbaru dan laporan riwayat.

### Sopir
- Melihat dan memperbarui jadwal keberangkatan.
- Mengonfirmasi, menyelesaikan, atau menghapus pesanan miliknya.
- Memantau riwayat pesanan yang sudah selesai atau dibatalkan.

### Penumpang
- Melihat jadwal yang tersedia dan membuat pesanan.
- Memeriksa status pesanan aktif serta riwayat perjalanan.
- Membatalkan pesanan dengan alasan yang jelas jika diperlukan.

## Tabel Database
- **users**: data akun pengguna beserta peran (admin, sopir, penumpang).
- **profils**: detail profil pengguna (telepon, pengalaman sopir, dsb.).
- **rutes**: daftar rute perjalanan.
- **sopirs**: data sopir beserta keterkaitan dengan pengguna.
- **jadwal_sopirs**: jadwal keberangkatan sopir per rute.
- **pesanans**: pesanan perjalanan penumpang beserta statusnya.

## Relasi Penting
- One-to-One: `users` ↔ `profils` (profil pengguna).
- One-to-One: `users` ↔ `sopirs` (akun sopir).
- One-to-Many: `sopirs` → `jadwal_sopirs`.
- One-to-Many: `rutes` → `jadwal_sopirs`.
- One-to-Many: `users` (penumpang) → `pesanans`.
- One-to-Many: `sopirs` → `pesanans`.
- One-to-Many: `rutes` → `pesanans`.
