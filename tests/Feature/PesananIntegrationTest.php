<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Rute;

class PesananIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function penumpang_bisa_membuat_pesanan_dan_tersimpan_di_database()
    {
        // --- SETUP (Persiapan Data Integrasi) ---
        // 1. Buat User Penumpang
        $penumpang = User::factory()->create(['role' => 'penumpang']);
        
        // 2. Buat Data Master Rute yang dibutuhkan untuk pesanan
        $rute = Rute::create([
            'nama_rute' => 'Rute Pagi',
            'asal' => 'Kampus',
            'tujuan' => 'Pasar',
            'jarak_km' => 10
        ]);

        // Login sebagai penumpang
        $this->actingAs($penumpang);

        // --- EXECUTION (Eksekusi Modul Controller) ---
        // Penumpang mengisi form pesanan
        $response = $this->post(route('penumpang.pesan'), [
            'user_id' => $penumpang->id,
            'rute_id' => $rute->id,
            'tanggal_keberangkatan' => '2025-06-01',
            'jam_keberangkatan' => '08:00',
            'status' => 'menunggu',
            'catatan' => 'Jemput di depan gerbang'
        ]);

        // --- ASSERTION (Verifikasi Integrasi Antar Modul) ---
        
        // 1. Pastikan Redirect kembali (Controller berhasil memproses)
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Pesanan berhasil disimpan.');

        // 2. Cek Database (Memastikan Model Pesanan terhubung dengan User & Rute)
        $this->assertDatabaseHas('pesanans', [
            'user_id' => $penumpang->id,
            'rute_id' => $rute->id,
            'status' => 'menunggu',
            'catatan' => 'Jemput di depan gerbang'
        ]);
    }
}