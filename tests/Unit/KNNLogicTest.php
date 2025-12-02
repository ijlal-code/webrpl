<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class KNNLogicTest extends TestCase
{
    // --- BAGIAN 1: Logika yang akan diuji (Disalin dari Controller) ---
    
    // Fungsi konversi jam ke menit
    private function jamToNumber($jam) {
        [$hour, $minute] = explode(':', $jam);
        return ((int) $hour) * 60 + (int) $minute;
    }

    // Rumus Jarak Euclidean
    private function euclideanDistance(array $a, array $b) {
        return sqrt(collect($a)->zip($b)->reduce(function ($carry, $pair) {
            [$x, $y] = $pair;
            return $carry + pow($x - $y, 2);
        }, 0));
    }

    // --- BAGIAN 2: Skenario Pengujian ---

    #[Test]
    public function test_konversi_jam_ke_menit_berjalan_benar()
    {
        // Kita tes: Apakah jam "01:30" benar-benar jadi angka 90?
        $input = "01:30";
        $hasilDiharapkan = 90; // (1 jam * 60) + 30 menit

        $hasilAktual = $this->jamToNumber($input);

        // Assert: Bandingkan hasil
        $this->assertEquals($hasilDiharapkan, $hasilAktual, "Logika konversi waktu salah.");
    }

    #[Test]
    public function test_perhitungan_jarak_euclidean_akurat()
    {
        // Kita tes rumus matematika Euclidean
        // Titik A(1, 1) dan Titik B(4, 5)
        // Jarak = sqrt((1-4)^2 + (1-5)^2) = sqrt(9 + 16) = sqrt(25) = 5
        $vectorA = [1, 1];
        $vectorB = [4, 5];
        $hasilDiharapkan = 5.0;

        $hasilAktual = $this->euclideanDistance($vectorA, $vectorB);

        $this->assertEquals($hasilDiharapkan, $hasilAktual, "Rumus Euclidean tidak akurat.");
    }
}