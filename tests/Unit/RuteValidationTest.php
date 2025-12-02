<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Rute; // Pastikan Model Rute diimport

class RuteValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function rute_harus_memiliki_nama_asal_dan_tujuan()
    {
        // SKENARIO: Menguji Validasi Input (Negative Test)
        // Admin mencoba menyimpan rute kosong, sistem harus menolak.

        // 1. Setup: Login sebagai Admin
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // 2. Eksekusi: Kirim data kosong ke route penyimpanan rute
        // Pastikan Anda memiliki route bernama 'rute.store' di web.php
        $response = $this->post(route('rute.store'), [
            'nama_rute' => '',
            'asal' => '',
            'tujuan' => '',
        ]);

        // 3. Verifikasi: Memastikan session memiliki error validasi
        $response->assertSessionHasErrors(['nama_rute', 'asal', 'tujuan']);
    }

    /** @test */
    public function rute_berhasil_disimpan_sesuai_model()
    {
        // SKENARIO: Menguji Penyimpanan Data Valid (Positive Test)
        
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Data input sesuai dengan $fillable di Model Rute Anda
        $inputData = [
            'nama_rute' => 'Majene - Polman',
            'asal' => 'Majene',
            'tujuan' => 'Polewali Mandar',
            'jarak_km' => 45.5,
            'perkiraan_waktu' => '1 Jam 30 Menit'
        ];

        // Kirim data
        $response = $this->post(route('rute.store'), $inputData);

        // Verifikasi Redirect sukses
        $response->assertRedirect();
        
        // Verifikasi Data masuk ke Database (sesuai Model Rute)
        $this->assertDatabaseHas('rutes', [
            'nama_rute' => 'Majene - Polman',
            'asal' => 'Majene',
            'jarak_km' => 45.5
        ]);
    }
}