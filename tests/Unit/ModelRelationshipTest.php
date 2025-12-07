<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Sopir;
use App\Models\Rute;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_memiliki_relasi_ke_sopir()
    {
        $user = User::factory()->create(['role' => 'sopir']);
        $sopir = Sopir::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'nomor_sim' => '12345',
            'telepon' => '08123',
        ]);

        $this->assertTrue($user->sopir->is($sopir));
    }

    #[Test]
    public function pesanan_memiliki_relasi_ke_penumpang_dan_rute()
    {
        $user = User::factory()->create(['role' => 'penumpang']);
        $rute = Rute::create([
            'nama_rute' => 'Test Rute',
            'asal' => 'A',
            'tujuan' => 'B',
        ]);

        $pesanan = Pesanan::create([
            'user_id' => $user->id,
            'rute_id' => $rute->id,
            'tanggal_keberangkatan' => '2025-01-01',
            'jam_keberangkatan' => '08:00',
            'status' => 'menunggu'
        ]);

        $this->assertTrue($pesanan->penumpang->is($user));
        $this->assertTrue($pesanan->rute->is($rute));
    }
}