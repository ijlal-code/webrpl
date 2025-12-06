<?php

namespace App\Http\Controllers;

use App\Models\JadwalSopir;
use App\Models\Kendaraan;
use App\Models\Pesanan;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Tampilkan ringkasan performa operasional untuk admin.
     */
    public function dashboard()
    {
        // Ringkas jumlah entitas penting agar admin cepat melihat kondisi sistem.
        $statistik = [
            'penumpang' => User::where('role', 'penumpang')->count(),
            'sopir' => User::where('role', 'sopir')->count(),
            'kendaraan' => Kendaraan::count(),
            'pesanan' => Pesanan::count(),
            'jadwal_aktif' => JadwalSopir::where('status', 'aktif')->count(),
        ];

        // Kirim data ke tampilan dashboard admin yang sudah diganti namanya lebih deskriptif.
        return view('admin.dashboard', [
            'statistik' => $statistik,
            'pesananTerbaru' => Pesanan::with(['penumpang', 'rute'])->latest()->take(5)->get(),
            'jadwalTerbaru' => JadwalSopir::with(['sopir.user', 'rute'])->latest()->take(5)->get(),
        ]);
    }

    /**
     * Tampilkan laporan lengkap beserta diagram deskriptif proses bisnis.
     */
    public function laporan()
    {
        // Ambil seluruh data pesanan untuk laporan serta ilustrasi proses bisnis berbentuk teks.
        $pesanan = Pesanan::with(['penumpang', 'sopir', 'kendaraan', 'rute'])->get();
        $diagrams = [
            'dfd0' => 'Penumpang ->[Pesan]-> Sistem ->[Kelola]-> Admin',
            'dfd1' => 'Penumpang ->[Kirim Data Pesanan]-> Proses Pemesanan ->[Notifikasi]-> Sopir',
            'dfd2' => 'Penumpang ->[Jam,Rute,Status]-> Modul KNN ->[Rekomendasi]-> Penumpang',
            'erd' => 'User(1)---(N)Pesanan(N)---(1)Rute; Pesanan(N)---(1)Kendaraan; Kendaraan(N)---(1)Sopir',
            'usecase' => 'Admin {Kelola Kendaraan, Kelola Sopir, Kelola Rute, Lihat Laporan}; Sopir {Konfirmasi Pesanan, Ubah Status Kendaraan}; Penumpang {Buat Pesanan, Lihat Status, Minta Rekomendasi}',
            'flowchart' => 'Mulai -> Login -> [Role?] Admin|Sopir|Penumpang -> Aksi sesuai role -> Selesai',
        ];

        // Tampilkan laporan pada halaman dashboard admin yang sama untuk konsistensi pengalaman.
        return view('admin.dashboard', [
            'statistik' => [
                'penumpang' => User::where('role', 'penumpang')->count(),
                'sopir' => User::where('role', 'sopir')->count(),
                'kendaraan' => Kendaraan::count(),
                'pesanan' => $pesanan->count(),
                'jadwal_aktif' => JadwalSopir::where('status', 'aktif')->count(),
            ],
            'pesananTerbaru' => $pesanan->take(5),
            'laporan' => $pesanan,
            'diagrams' => $diagrams,
            'jadwalTerbaru' => JadwalSopir::with(['sopir.user', 'rute'])->latest()->take(5)->get(),
        ]);
    }
}
