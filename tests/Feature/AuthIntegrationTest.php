<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase; // FITUR PEMBERSIH DIMATIKAN SEMENTARA
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class AuthIntegrationTest extends TestCase
{
    // Trait ini dikomentari agar data tidak dihapus setelah tes selesai
    use RefreshDatabase; 

    #[Test]
    public function user_bisa_register_sebagai_sopir_dan_data_tersimpan()
    {
        // 1. Siapkan Data Input (Gunakan email khusus agar mudah dicari di Database)
        $emailBukti = 'sopintuk_laporan@example.com';

        $dataInput = [
            'name' => 'SopirBukti Laporan',
            'email' => $emailBukti,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'sopir',
            'telepon' => '08999999999',
            'nomor_sim' => 'SIM-LAPORAN-001',
            'pengalaman' => '10 Tahun',
        ];

        // 2. Eksekusi: Kirim data ke route registrasi (POST)
        $response = $this->post(route('register.submit'), $dataInput);

        // 3. Verifikasi (Assertions)
        
        // a. Pastikan redirect ke halaman login (tanda sukses di controller)
        $response->assertRedirect(route('login'));

        // b. Cek apakah data masuk ke tabel 'users' (Modul Akun)
        // Fungsi ini akan mengecek langsung ke Database Asli Anda
        $this->assertDatabaseHas('users', [
            'email' => $emailBukti,
            'role' => 'sopir'
        ]);

        // c. Cek apakah data masuk ke tabel 'sopirs' (Modul Profil Sopir)
        // Mengambil user yang baru saja dibuat untuk memastikan relasi ID-nya benar
        $user = User::where('email', $emailBukti)->first();
        
        // Pastikan user ditemukan sebelum lanjut (agar tidak error jika gagal)
        $this->assertNotNull($user, 'User tidak ditemukan di database!');

        $this->assertDatabaseHas('sopirs', [
            'user_id' => $user->id, 
            'nomor_sim' => 'SIM-LAPORAN-001'
        ]);
    }
}