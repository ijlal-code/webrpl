<?php

namespace App\Http\Controllers;

use App\Models\JadwalSopir;
use App\Models\Pesanan;
use App\Models\Rute;
use Illuminate\Http\Request;

class SopirController extends Controller
{
    /**
     * Tampilkan dashboard untuk sopir dengan pesanan dan jadwal terbaru.
     */
    public function dashboard()
    {
        $sopirId = $this->getSopirId();

        // Halaman dashboard dipindahkan agar berada di folder sopir untuk kejelasan struktur.
        return view('sopir.dashboard', $this->dataSopir($sopirId));
    }

    /**
     * Tampilkan daftar jadwal milik sopir yang sedang login.
     */
    public function jadwal()
    {
        $sopirId = $this->getSopirId();

        return view('sopir.jadwal.index', $this->dataSopir($sopirId));
    }

    /**
     * Tampilkan pesanan yang perlu ditangani sopir.
     */
    public function pesanan()
    {
        $sopirId = $this->getSopirId();

        return view('sopir.pesanan.index', $this->dataSopir($sopirId));
    }

    /**
     * Tampilkan riwayat pesanan yang sudah selesai atau dibatalkan.
     */
    public function riwayat()
    {
        $sopirId = $this->getSopirId();

        return view('sopir.pesanan.riwayat', [
            'pesanan' => $this->riwayatPesananUntukSopir($sopirId),
        ]);
    }

    /**
     * Konfirmasi pesanan yang belum memiliki sopir atau memang milik sopir saat ini.
     */
    public function konfirmasi(Pesanan $pesanan)
    {
        $sopirId = $this->getSopirId();

        // Cegah sopir lain mengambil alih pesanan.
        if ($pesanan->sopir_id && $pesanan->sopir_id !== $sopirId) {
            abort(403);
        }

        $pesanan->update(['status' => 'dikonfirmasi']);

        return back()->with('success', 'Pesanan berhasil dikonfirmasi.');
    }

    /**
     * Konfirmasi seluruh pesanan yang masih menunggu untuk sopir yang sedang login.
     */
    public function konfirmasiSemua()
    {
        $sopirId = $this->getSopirId();

        $totalDikonfirmasi = Pesanan::where('sopir_id', $sopirId)
            ->where('status', 'menunggu')
            ->update(['status' => 'dikonfirmasi']);

        if ($totalDikonfirmasi === 0) {
            return back()->withErrors(['pesanan' => 'Tidak ada pesanan menunggu untuk dikonfirmasi.']);
        }

        return back()->with('success', "Berhasil mengonfirmasi {$totalDikonfirmasi} pesanan menunggu.");
    }

    /**
     * Tandai pesanan telah selesai oleh sopir yang sesuai.
     */
    public function selesaikan(Pesanan $pesanan)
    {
        $sopirId = $this->getSopirId();

        if ($pesanan->sopir_id !== $sopirId) {
            abort(403);
        }

        $pesanan->update(['status' => 'selesai']);

        $jadwal = $pesanan->jadwal;
        $jadwalDihapus = false;

        if ($jadwal) {
            $jadwal->update(['status' => 'selesai']);
            $jadwalDihapus = $this->hapusJadwalJikaSelesai($jadwal);
        }

        $pesan = $jadwalDihapus
            ? 'Pesanan selesai dan jadwal terkait dihapus dari daftar.'
            : 'Pesanan telah ditandai selesai.';

        return back()->with('success', $pesan);
    }

    /**
     * Hapus pesanan yang sudah selesai agar daftar sopir lebih ringkas.
     */
    public function hapusPesanan(Pesanan $pesanan)
    {
        $sopirId = $this->getSopirId();

        if ($pesanan->sopir_id !== $sopirId) {
            abort(403);
        }

        if (!in_array($pesanan->status, ['selesai', 'dibatalkan'])) {
            return back()->withErrors(['pesanan' => 'Pesanan hanya bisa dihapus setelah selesai atau dibatalkan.']);
        }

        $pesanan->delete();

        return back()->with('success', 'Pesanan selesai dihapus dari daftar.');
    }

    /**
     * Hapus seluruh riwayat pesanan untuk sopir yang sedang login.
     */
    public function bersihkanRiwayat()
    {
        $sopirId = $this->getSopirId();

        Pesanan::where('sopir_id', $sopirId)
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->delete();

        return back()->with('success', 'Riwayat pesanan berhasil dibersihkan.');
    }

    /**
     * Form untuk mengedit jadwal sopir tertentu.
     */
    public function editJadwal(JadwalSopir $jadwal)
    {
        $sopirId = $this->getSopirId();

        if ($jadwal->sopir_id !== $sopirId) {
            abort(403);
        }

        return view('sopir.jadwal.edit', [
            'jadwal' => $jadwal->load('rute'),
        ]);
    }

    /**
     * Simpan jadwal baru yang diajukan sopir.
     */
    public function simpanJadwal(Request $request)
    {
        $data = $request->validate([
            'rute_pilihan' => 'required|in:majene_polewali,polewali_majene,custom',
            'custom_rute' => 'required_if:rute_pilihan,custom|nullable|string|max:255',
            'tanggal_keberangkatan' => 'required|date',
            'jam_keberangkatan' => 'required',
            'status' => 'required|in:siap_berangkat,dalam_perjalanan,selesai',
            'catatan' => 'nullable|string',
        ]);

        $sopirId = $this->getSopirId();

        // Tidak boleh membuat jadwal tanpa profil sopir yang valid.
        if (!$sopirId) {
            return back()->withErrors(['jadwal' => 'Sopir tidak ditemukan.']);
        }

        $rute = $this->resolveRute($data['rute_pilihan'], $data['custom_rute'] ?? null);

        JadwalSopir::create([
            'sopir_id' => $sopirId,
            'rute_id' => $rute->id,
            'tanggal_keberangkatan' => $data['tanggal_keberangkatan'],
            'jam_keberangkatan' => $data['jam_keberangkatan'],
            'status' => $data['status'],
            'catatan' => $data['catatan'],
        ]);

        return back()->with('success', 'Jadwal keberangkatan tersimpan.');
    }

    /**
     * Perbarui jadwal yang sudah ada milik sopir saat ini.
     */
    public function perbaruiJadwal(JadwalSopir $jadwal, Request $request)
    {
        $data = $request->validate([
            'rute_pilihan' => 'sometimes|required|in:majene_polewali,polewali_majene,custom',
            'custom_rute' => 'required_if:rute_pilihan,custom|nullable|string|max:255',
            'tanggal_keberangkatan' => 'sometimes|required|date',
            'jam_keberangkatan' => 'sometimes|required',
            'status' => 'required|in:siap_berangkat,dalam_perjalanan,selesai',
            'catatan' => 'nullable|string',
        ]);

        $sopirId = $this->getSopirId();

        if ($jadwal->sopir_id !== $sopirId) {
            abort(403);
        }

        // Perbarui rute hanya jika input baru diberikan.
        if (isset($data['rute_pilihan'])) {
            $rute = $this->resolveRute($data['rute_pilihan'], $data['custom_rute'] ?? null);
            $jadwal->rute_id = $rute->id;
        }

        if (isset($data['tanggal_keberangkatan'])) {
            $jadwal->tanggal_keberangkatan = $data['tanggal_keberangkatan'];
        }

        if (isset($data['jam_keberangkatan'])) {
            $jadwal->jam_keberangkatan = $data['jam_keberangkatan'];
        }

        $pesan = 'Jadwal diperbarui.';

        if ($data['status'] === 'selesai') {
            $pesananDikonfirmasi = $jadwal->pesanan()->where('status', 'dikonfirmasi')->get();

            if ($pesananDikonfirmasi->isNotEmpty()) {
                foreach ($pesananDikonfirmasi as $pesanan) {
                    $pesanan->update(['status' => 'selesai']);
                }

                $pesan = 'Jadwal dan pesanan terkonfirmasi ditandai selesai.';
            } elseif (!$jadwal->pesanan()->exists()) {
                $jadwal->delete();

                return redirect()->route('sopir.jadwal.index')->with('success', 'Jadwal tanpa pesanan dihapus.');
            }

            if ($this->hapusJadwalJikaSelesai($jadwal)) {
                return redirect()->route('sopir.jadwal.index')->with('success', 'Jadwal selesai dihapus setelah pesanan tuntas.');
            }
        }

        $jadwal->status = $data['status'];
        $jadwal->catatan = $data['catatan'] ?? null;
        $jadwal->save();

        return redirect()->route('sopir.jadwal.index')->with('success', $pesan);
    }

    /**
     * Hapus jadwal sopir yang dipilih.
     */
    public function hapusJadwal(JadwalSopir $jadwal)
    {
        $sopirId = $this->getSopirId();

        if ($jadwal->sopir_id !== $sopirId) {
            abort(403);
        }

        $jadwal->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * Tentukan rute berdasarkan pilihan preset atau input khusus.
     */
    private function resolveRute(string $pilihan, ?string $customRute): Rute
    {
        return match ($pilihan) {
            'majene_polewali' => Rute::firstOrCreate(
                ['nama_rute' => 'Majene - Polewali'],
                ['asal' => 'Majene', 'tujuan' => 'Polewali']
            ),
            'polewali_majene' => Rute::firstOrCreate(
                ['nama_rute' => 'Polewali - Majene'],
                ['asal' => 'Polewali', 'tujuan' => 'Majene']
            ),
            'custom' => $this->buatRuteCustom($customRute),
        };
    }

    /**
     * Buat entri rute baru berdasarkan input bebas pengguna.
     */
    private function buatRuteCustom(?string $input): Rute
    {
        $input = trim($input ?? '');

        if ($input === '') {
            abort(422, 'Rute khusus harus diisi.');
        }

        [$asal, $tujuan] = array_pad(array_map('trim', explode('-', $input, 2)), 2, null);

        return Rute::firstOrCreate(
            ['nama_rute' => $input],
            [
                'asal' => $asal ?: $input,
                'tujuan' => $tujuan ?: ($asal ?: $input),
            ]
        );
    }

    /**
     * Kumpulan data yang berulang untuk setiap tampilan sopir.
     */
    private function dataSopir(?int $sopirId): array
    {
        return [
            'pesanan' => $this->pesananAktifUntukSopir($sopirId),
            'jadwal' => $this->jadwalUntukSopir($sopirId),
        ];
    }

    /**
     * Ambil daftar pesanan yang terkait dengan sopir.
     */
    private function pesananAktifUntukSopir(?int $sopirId)
    {
        return Pesanan::with(['penumpang.profil', 'rute', 'kendaraan', 'jadwal'])
            ->where('sopir_id', $sopirId)
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->latest()
            ->get();
    }

    /**
     * Ambil daftar riwayat pesanan sopir.
     */
    private function riwayatPesananUntukSopir(?int $sopirId)
    {
        return Pesanan::with(['penumpang', 'rute', 'kendaraan', 'jadwal'])
            ->where('sopir_id', $sopirId)
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->latest()
            ->get();
    }

    /**
     * Ambil semua jadwal yang dimiliki sopir terurut dari yang terbaru.
     */
    private function jadwalUntukSopir(?int $sopirId)
    {
        return JadwalSopir::with('rute')
            ->withCount([
                'pesanan',
                'pesanan as pesanan_dikonfirmasi_count' => fn($q) => $q->where('status', 'dikonfirmasi'),
            ])
            ->where('sopir_id', $sopirId)
            ->orderByDesc('tanggal_keberangkatan')
            ->orderByDesc('jam_keberangkatan')
            ->get();
    }

    /**
     * Ambil ID sopir dari user yang sedang login.
     */
    private function getSopirId(): ?int
    {
        return auth()->user()->sopir->id ?? null;
    }

    /**
     * Hapus jadwal jika tidak ada pesanan aktif yang masih menunggu atau dikonfirmasi.
     */
    private function hapusJadwalJikaSelesai(JadwalSopir $jadwal): bool
    {
        $masihAktif = $jadwal->pesanan()->whereIn('status', ['menunggu', 'dikonfirmasi'])->exists();

        if ($masihAktif) {
            return false;
        }

        $jadwal->delete();

        return true;
    }
}
