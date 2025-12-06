<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\JadwalSopir;
use Illuminate\Http\Request;

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

        // Hitung rekomendasi dan pemetaan pesanan per jadwal agar tampilan lebih mudah dipahami.
        $rekomendasi = $this->buildRekomendasi(auth()->id());

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

        if ($jadwal->status !== 'aktif') {
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

    /**
     * Daftar pesanan penumpang beserta relasi pentingnya.
     */
    public function pesanan()
    {
        $pesanan = Pesanan::with(['rute', 'kendaraan', 'sopir', 'jadwal'])
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
        $pesanan = Pesanan::with(['rute', 'kendaraan', 'sopir', 'jadwal'])
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

    /**
     * Hitung rekomendasi jadwal berdasarkan riwayat pemesanan penumpang.
     */
    private function buildRekomendasi(int $userId)
    {
        $riwayat = Pesanan::select('rute_id', 'jam_keberangkatan')
            ->selectRaw('count(*) as total')
            ->where('user_id', $userId)
            ->groupBy('rute_id', 'jam_keberangkatan')
            ->orderByDesc('total')
            ->take(3)
            ->get();

        if ($riwayat->isEmpty()) {
            return JadwalSopir::with(['sopir.user', 'rute'])
                ->where('status', 'aktif')
                ->orderBy('tanggal_keberangkatan')
                ->orderBy('jam_keberangkatan')
                ->take(3)
                ->get();
        }

        $jadwal = JadwalSopir::with(['sopir.user', 'rute'])
            ->where('status', 'aktif')
            ->where(function ($query) use ($riwayat) {
                foreach ($riwayat as $preferensi) {
                    $query->orWhere(function ($sub) use ($preferensi) {
                        $sub->where('rute_id', $preferensi->rute_id)
                            ->where('jam_keberangkatan', $preferensi->jam_keberangkatan);
                    });
                }
            })
            ->orderBy('tanggal_keberangkatan')
            ->orderBy('jam_keberangkatan')
            ->get();

        return $jadwal->isNotEmpty() ? $jadwal : JadwalSopir::with(['sopir.user', 'rute'])
            ->where('status', 'aktif')
            ->orderBy('tanggal_keberangkatan')
            ->orderBy('jam_keberangkatan')
            ->take(3)
            ->get();
    }
}
