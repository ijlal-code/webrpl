<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\JadwalSopir;
use Illuminate\Http\Request;
// use App\Models\Rute; // Rute diasumsikan sudah ada di dalam model Pesanan/Jadwal

class UserController extends Controller
{
    /**
     * Halaman utama penumpang berisi jadwal dan rekomendasi perjalanan.
     */
    public function dashboard()
    {
        $jadwal = JadwalSopir::with(['sopir.user', 'rute'])
            ->orderBy('tanggal_keberangkatan')
            ->orderBy('jam_keberangkatan')
            ->get();

        // **MODIFIKASI:** Panggil logika Rekomendasi KNN
        $rekomendasi = $this->getRekomendasiKNN(auth()->id());

        $pesananPerJadwal = Pesanan::where('user_id', auth()->id())
            ->get()
            ->keyBy('jadwal_id');

        // View dipindah ke folder penumpang agar struktur mengikuti peran pengguna.
        return view('penumpang.dashboard', [
            'jadwal' => $jadwal,
            'rekomendasi' => $rekomendasi,
            'pesananPerJadwal' => $pesananPerJadwal,
        ]);
    }

    /**
     * Buat pesanan baru berdasarkan jadwal yang dipilih penumpang.
     */
    public function buatPesanan(Request $request)
    {
        $data = $request->validate([
            'jadwal_id' => 'required|exists:jadwal_sopirs,id',
            'catatan' => 'nullable|string',
        ]);

        $jadwal = JadwalSopir::with('sopir')->findOrFail($data['jadwal_id']);

        if ($jadwal->status !== 'siap_berangkat') {
            return back()->withErrors(['jadwal_id' => 'Jadwal ini tidak tersedia untuk dipesan.']);
        }

        // Pastikan penumpang tidak memesan jadwal yang sama lebih dari sekali.
        $sudahDipesan = Pesanan::where('user_id', auth()->id())
            ->where('jadwal_id', $jadwal->id)
            ->exists();

        if ($sudahDipesan) {
            return back()->withErrors(['jadwal_id' => 'Anda sudah memesan jadwal ini.']);
        }

        Pesanan::create([
            'user_id' => auth()->id(),
            'sopir_id' => $jadwal->sopir_id,
            'jadwal_id' => $jadwal->id,
            'rute_id' => $jadwal->rute_id,
            'tanggal_keberangkatan' => $jadwal->tanggal_keberangkatan,
            'jam_keberangkatan' => $jadwal->jam_keberangkatan,
            'status' => 'menunggu',
            'catatan' => $data['catatan'] ?? null,
        ]);

        return redirect()->route('penumpang.pesanan')->with('success', 'Pesanan berhasil dibuat.');
    }

    // ... (Metode pesanan(), batalkanPesanan(), jadwal(), riwayat(), hapusRiwayat(), bersihkanRiwayat() tetap sama) ...
    // Saya memangkasnya di sini untuk fokus pada KNN.

    /**
     * Daftar pesanan penumpang beserta relasi pentingnya.
     */
    public function pesanan()
    {
        $pesanan = Pesanan::with(['rute', 'sopir', 'jadwal'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->latest()
            ->get();

        return view('penumpang.pesanan', [
            'pesanan' => $pesanan,
        ]);
    }

    /**
     * Batalkan pesanan milik penumpang dengan alasan yang jelas.
     */
    public function batalkanPesanan(Request $request, Pesanan $pesanan)
    {
        if ($pesanan->user_id !== auth()->id()) {
            abort(403);
        }

        if (in_array($pesanan->status, ['selesai', 'dibatalkan'])) {
            return back()->withErrors(['pesanan' => 'Pesanan ini tidak dapat dibatalkan.']);
        }

        $data = $request->validate([
            'alasan' => 'required|in:perubahan_rencana,menemukan_transportasi_lain,kesalahan_pemesanan,lainnya',
            'alasan_lain' => 'required_if:alasan,lainnya|nullable|string|max:255',
        ]);

        $alasan = [
            'perubahan_rencana' => 'Perubahan rencana perjalanan',
            'menemukan_transportasi_lain' => 'Menemukan transportasi lain',
            'kesalahan_pemesanan' => 'Kesalahan saat pemesanan',
            'lainnya' => trim($data['alasan_lain'] ?? 'Alasan lain'),
        ][$data['alasan']];

        $pesanan->update([
            'status' => 'dibatalkan',
            'alasan_pembatalan' => $alasan,
        ]);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    /**
     * Semua jadwal sopir yang bisa dipesan dengan pencarian.
     */
    public function jadwal(Request $request)
    {
        $search = trim($request->get('q', ''));

        $jadwal = JadwalSopir::with(['sopir.user', 'rute'])
            ->whereIn('status', ['siap_berangkat', 'dalam_perjalanan'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('rute', function ($ruteQuery) use ($search) {
                        $ruteQuery->where('nama_rute', 'like', "%{$search}%")
                            ->orWhere('asal', 'like', "%{$search}%")
                            ->orWhere('tujuan', 'like', "%{$search}%");
                    })
                        ->orWhereHas('sopir', function ($sopirQuery) use ($search) {
                            $sopirQuery->where('nama', 'like', "%{$search}%");
                        })
                        ->orWhereHas('sopir.user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhere('tanggal_keberangkatan', 'like', "%{$search}%")
                        ->orWhere('jam_keberangkatan', 'like', "%{$search}%");
                });
            })
            ->orderBy('tanggal_keberangkatan')
            ->orderBy('jam_keberangkatan')
            ->get();

        $pesananPerJadwal = Pesanan::where('user_id', auth()->id())
            ->get()
            ->keyBy('jadwal_id');

        return view('penumpang.jadwal', [
            'jadwal' => $jadwal,
            'pesananPerJadwal' => $pesananPerJadwal,
            'search' => $search,
        ]);
    }

    /**
     * Riwayat pesanan penumpang yang telah selesai atau dibatalkan.
     */
    public function riwayat()
    {
        $pesanan = Pesanan::with(['rute', 'sopir', 'jadwal'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->latest()
            ->get();

        return view('penumpang.riwayat', [
            'pesanan' => $pesanan,
        ]);
    }

    /**
     * Hapus entri riwayat yang tidak lagi ingin ditampilkan pengguna.
     */
    public function hapusRiwayat(Pesanan $pesanan)
    {
        if ($pesanan->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($pesanan->status, ['selesai', 'dibatalkan'])) {
            return back()->withErrors(['pesanan' => 'Riwayat hanya bisa dihapus ketika status selesai atau dibatalkan.']);
        }

        $pesanan->delete();

        return back()->with('success', 'Riwayat pesanan dihapus.');
    }

    /**
     * Hapus seluruh riwayat pesanan selesai atau dibatalkan milik pengguna.
     */
    public function bersihkanRiwayat()
    {
        Pesanan::where('user_id', auth()->id())
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->delete();

        return back()->with('success', 'Semua riwayat pesanan dibersihkan.');
    }

    // =========================================================================
    // **START: LOGIKA KNN (Menggantikan buildRekomendasi lama)**
    // =========================================================================

    /**
     * Hitung rekomendasi jadwal berdasarkan algoritma KNN.
     * Mengambil input dari riwayat pesanan terakhir pengguna.
     */
    private function getRekomendasiKNN(int $userId): \Illuminate\Support\Collection
    {
        $lastOrder = Pesanan::where('user_id', $userId)
            ->whereIn('status', ['dikonfirmasi', 'selesai'])
            ->latest('created_at')->first();

        // Fallback: Jika tidak ada riwayat yang valid, kembalikan 3 jadwal aktif teratas
        if (!$lastOrder) {
            return JadwalSopir::with(['sopir.user', 'rute'])
                ->where('status', 'siap_berangkat')
                ->orderBy('tanggal_keberangkatan')
                ->orderBy('jam_keberangkatan')
                ->take(3)
                ->get();
        }

        // 1. Tentukan Vektor Input berdasarkan riwayat terakhir
        $inputVector = [
            $this->jamToNumber($lastOrder->jam_keberangkatan),
            $this->ruteToNumber($lastOrder->rute_id),
            $this->statusToNumber($lastOrder->status),
        ];

        $dataset = $this->buildDatasetKNN();
        $k = 3; // Nilai K default untuk dashboard

        // 2. Cari Tetangga Terdekat
        $neighbors = $this->nearestNeighbors($inputVector, $dataset, $k);

        // 3. Votasi untuk mendapatkan Label (jam keberangkatan) terbaik
        $rekomendasiJam = collect($neighbors)
            ->groupBy('label')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first();

        // 4. Cari Jadwal Sopir AKTIF yang cocok dengan jam rekomendasi tersebut
        $jadwal = JadwalSopir::with(['sopir.user', 'rute'])
            ->where('status', 'siap_berangkat')
            ->where('jam_keberangkatan', $rekomendasiJam)
            ->orderBy('tanggal_keberangkatan')
            ->orderBy('jam_keberangkatan')
            ->get();
            
        // Fallback jika tidak ditemukan jadwal aktif dengan jam yang direkomendasikan
        return $jadwal->isNotEmpty() ? $jadwal : JadwalSopir::with(['sopir.user', 'rute'])
            ->where('status', 'siap_berangkat')
            ->orderBy('tanggal_keberangkatan')
            ->orderBy('jam_keberangkatan')
            ->take(3)
            ->get();
    }

    private function buildDatasetKNN(): array
    {
        $history = Pesanan::with('rute')
            ->whereIn('status', ['dikonfirmasi', 'selesai'])
            ->get();

        if ($history->isEmpty()) {
            // Dataset dummy disalin dari RekomendasiKNNController.php
            return [
                ['features' => [$this->jamToNumber('07:00'), $this->ruteToNumber(1), $this->statusToNumber('dikonfirmasi')], 'label' => '08:00'],
                ['features' => [$this->jamToNumber('09:00'), $this->ruteToNumber(2), $this->statusToNumber('selesai')], 'label' => '09:30'],
                ['features' => [$this->jamToNumber('13:00'), $this->ruteToNumber(3), $this->statusToNumber('menunggu')], 'label' => '14:00'],
                ['features' => [$this->jamToNumber('15:00'), $this->ruteToNumber(1), $this->statusToNumber('selesai')], 'label' => '15:30'],
                ['features' => [$this->jamToNumber('17:00'), $this->ruteToNumber(2), $this->statusToNumber('dikonfirmasi')], 'label' => '17:30'],
            ];
        }

        return $history->map(function ($pesanan) {
            return [
                'features' => [
                    $this->jamToNumber($pesanan->jam_keberangkatan),
                    $this->ruteToNumber($pesanan->rute_id),
                    $this->statusToNumber($pesanan->status),
                ],
                'label' => $pesanan->jam_keberangkatan,
            ];
        })->toArray();
    }

    private function nearestNeighbors(array $input, array $dataset, int $k): array
    {
        $scored = collect($dataset)->map(function ($row) use ($input) {
            $distance = $this->euclideanDistance($input, $row['features']);
            return [
                'label' => $row['label'],
                'distance' => $distance,
            ];
        })->sortBy('distance')->values()->take($k);

        return $scored->toArray();
    }

    private function euclideanDistance(array $a, array $b): float
    {
        return sqrt(collect($a)->zip($b)->reduce(function ($carry, $pair) {
            [$x, $y] = $pair;
            return $carry + pow($x - $y, 2);
        }, 0));
    }

    private function jamToNumber(string $jam): int
    {
        [$hour, $minute] = explode(':', $jam);
        return ((int) $hour) * 60 + (int) $minute;
    }

    private function ruteToNumber(int $ruteId): int
    {
        return $ruteId;
    }

    private function statusToNumber(string $status): int
    {
        return match ($status) {
            'menunggu' => 0,
            'dikonfirmasi' => 1,
            'selesai' => 2,
            'dibatalkan' => 3,
            default => 0,
        };
    }
    // =========================================================================
    // **END: LOGIKA KNN**
    // =========================================================================
}