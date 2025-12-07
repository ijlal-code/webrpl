<?php

namespace App\Http\Controllers;

use App\Models\JadwalSopir;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Rute;
use Illuminate\Http\Request;

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
            'pesanan' => Pesanan::count(),
            'jadwal_aktif' => JadwalSopir::where('status', 'siap_berangkat')->count(),
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
        $pesanan = Pesanan::with(['penumpang', 'sopir', 'rute'])->get();
        $diagrams = [
            'dfd0' => 'Penumpang ->[Pesan]-> Sistem ->[Kelola]-> Admin',
            'dfd1' => 'Penumpang ->[Kirim Data Pesanan]-> Proses Pemesanan ->[Notifikasi]-> Sopir',
            'dfd2' => 'Penumpang ->[Jam,Rute,Status]-> Modul KNN ->[Rekomendasi]-> Penumpang',
            'erd' => 'User(1)---(N)Pesanan(N)---(1)Rute; Pesanan(N)---(1)Sopir',
            'usecase' => 'Admin {Kelola Sopir, Kelola Rute, Lihat Laporan}; Sopir {Konfirmasi Pesanan}; Penumpang {Buat Pesanan, Lihat Status, Minta Rekomendasi}',
            'flowchart' => 'Mulai -> Login -> [Role?] Admin|Sopir|Penumpang -> Aksi sesuai role -> Selesai',
        ];

        // Tampilkan laporan pada halaman dashboard admin yang sama untuk konsistensi pengalaman.
        return view('admin.dashboard', [
            'statistik' => [
                'penumpang' => User::where('role', 'penumpang')->count(),
                'sopir' => User::where('role', 'sopir')->count(),
                'pesanan' => $pesanan->count(),
                'jadwal_aktif' => JadwalSopir::where('status', 'siap_berangkat')->count(),
            ],
            'pesananTerbaru' => $pesanan->take(5),
            'laporan' => $pesanan,
            'diagrams' => $diagrams,
            'jadwalTerbaru' => JadwalSopir::with(['sopir.user', 'rute'])->latest()->take(5)->get(),
        ]);
    }

    /**
     * Tampilkan daftar pesanan dengan pencarian ringan dan aksi hapus.
     */
    public function pesanan(Request $request)
    {
        $keyword = $request->query('q');

        $pesanan = Pesanan::with(['penumpang', 'sopir', 'rute', 'jadwal'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($sub) use ($keyword) {
                    $sub->whereHas('penumpang', fn($q) => $q->where('name', 'like', "%{$keyword}%"))
                        ->orWhereHas('sopir', fn($q) => $q->where('nama', 'like', "%{$keyword}%"))
                        ->orWhereHas('rute', fn($q) => $q->where('nama_rute', 'like', "%{$keyword}%"));
                });
            })
            ->latest()
            ->get();

        return view('pesanan.index', [
            'pesanan' => $pesanan,
            'keyword' => $keyword,
        ]);
    }

    /**
     * Kelola rute yang dibuat sopir.
     */
    public function rute()
    {
        return view('admin.rute', [
            'rute' => Rute::withCount(['jadwal', 'pesanans'])->latest()->get(),
        ]);
    }

    public function hapusRute(Rute $rute)
    {
        $rute->delete();

        return back()->with('success', 'Rute berhasil dihapus.');
    }

    /**
     * Kelola profil pengguna penumpang dan sopir.
     */
    public function pengguna()
    {
        return view('admin.pengguna', [
            'penumpang' => User::with('profil')->where('role', 'penumpang')->get(),
            'sopir' => User::with(['profil', 'sopir'])->where('role', 'sopir')->get(),
        ]);
    }

    public function hapusPengguna(User $user)
    {
        if ($user->role === 'admin') {
            abort(403);
        }

        $user->delete();

        return back()->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
