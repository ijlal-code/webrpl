@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-1">Dashboard Penumpang</h1>
    <p class="text-muted mb-4">Pantau jadwal angkutan umum Mobil Majene dan pesan sopir yang sedang aktif.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Kolom kiri memuat rekomendasi otomatis, kanan berisi ringkasan status sopir --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">Rekomendasi Jadwal Anda</div>
                <div class="card-body">
                    @if($rekomendasi->isEmpty())
                        <p class="text-muted mb-0">Belum ada histori perjalanan yang bisa dianalisis. Pantau jadwal sopir aktif di bawah untuk melakukan pemesanan.</p>
                    @else
                        <form method="POST" action="{{ route('penumpang.pesan') }}" class="d-flex flex-column gap-3">
                            @csrf
                            <div>
                                <label class="form-label">Pilih jadwal</label>
                                <select name="jadwal_id" class="form-select" required>
                                    @foreach($rekomendasi as $item)
                                        @php $sudahDipesan = $pesananPerJadwal->has($item->id); @endphp
                                        <option value="{{ $item->id }}" @selected(old('jadwal_id') == $item->id) @disabled($sudahDipesan)>
                                            {{ $item->rute->nama_rute ?? '-' }} • {{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }} • Sopir {{ $item->sopir->nama ?? $item->sopir->user->name ?? '-' }}
                                            @if($item->catatan)
                                                • Catatan sopir: {{ $item->catatan }}
                                            @endif
                                            @if($sudahDipesan)
                                                • Sudah dipesan
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Catatan untuk sopir (opsional)</label>
                                <input type="text" name="catatan" class="form-control" value="{{ old('catatan') }}" placeholder="Contoh: mohon tunggu pak">
                            </div>
                            <button class="btn btn-primary" type="submit">Pesan Jadwal Rekomendasi</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">Ringkasan Aktivitas Sopir</div>
                <div class="card-body">
                    @php
                        $ringkasan = $jadwal->groupBy('status')->map->count();
                        $statusLabel = [
                            'siap_berangkat' => 'Siap berangkat',
                            'dalam_perjalanan' => 'Dalam perjalanan',
                            'selesai' => 'Selesai',
                        ];
                        $badgeClass = [
                            'siap_berangkat' => 'success',
                            'dalam_perjalanan' => 'warning',
                            'selesai' => 'secondary',
                        ];
                    @endphp
                    <div class="row text-center g-3">
                        @foreach($statusLabel as $status => $label)
                            <div class="col-4">
                                <div class="border rounded p-3 h-100">
                                    <p class="text-uppercase small mb-1">{{ $label }}</p>
                                    <span class="display-6 d-block">{{ $ringkasan[$status] ?? 0 }}</span>
                                    <span class="badge text-bg-{{ $badgeClass[$status] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Aksi Cepat</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h5>Jadwal Sopir</h5>
                        <p class="text-muted">Lihat semua jadwal sopir dan lakukan pemesanan dengan pencarian cepat.</p>
                        <a href="{{ route('penumpang.jadwal') }}" class="btn btn-outline-primary w-100">Lihat Jadwal</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h5>Pesanan Aktif</h5>
                        <p class="text-muted">Pantau pesanan yang masih menunggu atau sudah dikonfirmasi.</p>
                        <a href="{{ route('penumpang.pesanan') }}" class="btn btn-outline-primary w-100">Buka Pesanan</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h5>Riwayat Pesanan</h5>
                        <p class="text-muted">Tinjau pesanan yang telah selesai atau dibatalkan, dan kelola riwayat Anda.</p>
                        <a href="{{ route('penumpang.riwayat') }}" class="btn btn-outline-primary w-100">Lihat Riwayat</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
