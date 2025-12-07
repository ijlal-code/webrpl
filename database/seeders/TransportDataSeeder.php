<?php

namespace Database\Seeders;

use App\Models\JadwalSopir;
use App\Models\Pesanan;
use App\Models\Rute;
use App\Models\Sopir;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TransportDataSeeder extends Seeder
{
    public function run(): void
    {
        $ruteList = collect([
            ['nama_rute' => 'Majene - Polewali', 'asal' => 'Majene', 'tujuan' => 'Polewali', 'jarak_km' => 120, 'perkiraan_waktu' => '02:30'],
            ['nama_rute' => 'Polewali - Majene', 'asal' => 'Polewali', 'tujuan' => 'Majene', 'jarak_km' => 120, 'perkiraan_waktu' => '02:30'],
            ['nama_rute' => 'Mamuju - Majene', 'asal' => 'Mamuju', 'tujuan' => 'Majene', 'jarak_km' => 100, 'perkiraan_waktu' => '02:00'],
        ])->map(fn (array $data) => Rute::firstOrCreate(['nama_rute' => $data['nama_rute']], $data));

        if ($ruteList->isEmpty()) {
            return;
        }

        if (Sopir::count() === 0) {
            $this->call(UserRoleSeeder::class);
        }

        $penumpang = User::where('role', 'penumpang')->get();

        if ($penumpang->isEmpty()) {
            $penumpang = User::factory()->count(5)->penumpang()->create(['password' => Hash::make('password')]);
        }

        $sopirList = Sopir::with('user')->get();

        $jadwalList = collect();
        $statusJadwal = ['siap_berangkat', 'dalam_perjalanan'];

        foreach ($sopirList as $index => $sopir) {
            foreach ($statusJadwal as $offset => $status) {
                $rute = $ruteList[($index + $offset) % $ruteList->count()];
                $tanggal = Carbon::now()->addDays($index + $offset)->toDateString();
                $jam = Carbon::now()->addHours($offset * 2 + 8)->format('H:i');

                $jadwalList->push(
                    JadwalSopir::updateOrCreate(
                        [
                            'sopir_id' => $sopir->id,
                            'rute_id' => $rute->id,
                            'tanggal_keberangkatan' => $tanggal,
                            'jam_keberangkatan' => $jam,
                        ],
                        [
                            'status' => $status,
                            'catatan' => 'Terbuat otomatis untuk demo massal',
                        ]
                    )
                );
            }
        }

        $statusPesanan = ['menunggu', 'dikonfirmasi'];

        foreach ($jadwalList as $index => $jadwal) {
            $penumpangTerpilih = $penumpang[$index % $penumpang->count()];
            $status = $statusPesanan[$index % count($statusPesanan)];

            Pesanan::updateOrCreate(
                [
                    'user_id' => $penumpangTerpilih->id,
                    'jadwal_id' => $jadwal->id,
                ],
                [
                    'sopir_id' => $jadwal->sopir_id,
                    'rute_id' => $jadwal->rute_id,
                    'tanggal_keberangkatan' => $jadwal->tanggal_keberangkatan,
                    'jam_keberangkatan' => $jadwal->jam_keberangkatan,
                    'status' => $status,
                    'catatan' => 'Pesanan otomatis #' . ($index + 1),
                ]
            );

            if ($status === 'dikonfirmasi') {
                $penumpangKedua = $penumpang[($index + 1) % $penumpang->count()];

                Pesanan::updateOrCreate(
                    [
                        'user_id' => $penumpangKedua->id,
                        'jadwal_id' => $jadwal->id,
                        'catatan' => 'Pesanan tambahan #' . ($index + 1),
                    ],
                    [
                        'sopir_id' => $jadwal->sopir_id,
                        'rute_id' => $jadwal->rute_id,
                        'tanggal_keberangkatan' => $jadwal->tanggal_keberangkatan,
                        'jam_keberangkatan' => $jadwal->jam_keberangkatan,
                        'status' => 'dikonfirmasi',
                    ]
                );
            }
        }
    }
}
