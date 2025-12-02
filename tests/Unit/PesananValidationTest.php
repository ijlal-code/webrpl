<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class PesananValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_tidak_bisa_membuat_pesanan_kosong()
    {
        // 1. Setup: Login sebagai Admin
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // 2. Eksekusi: Kirim data kosong ke route 'pesanan.store'
        // Route ini SUDAH ADA di file web.php Anda
        $response = $this->post(route('pesanan.store'), [
            'user_id' => '', // Sengaja dikosongkan
            'status' => '',  // Sengaja dikosongkan
        ]);

        // 3. Verifikasi: Harapannya ada error validasi
        // Ini sesuai materi: Memastikan logika validasi berfungsi ("Unit")
        $response->assertSessionHasErrors(['user_id', 'status']);
    }
}