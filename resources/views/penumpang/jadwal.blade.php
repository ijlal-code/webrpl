@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Semua Jadwal Sopir</h1>
    <p class="text-muted">Gunakan pencarian untuk menemukan jadwal berdasarkan rute, nama sopir, atau waktu keberangkatan.</p>

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

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('penumpang.jadwal') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Kata kunci</label>
                    <input type="text" name="q" class="form-control" placeholder="Cari rute, sopir, tanggal, atau jam" value="{{ $search }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1" type="submit">Cari</button>
                    <a href="{{ route('penumpang.jadwal') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @php
        $badgeClass = [
            'siap_berangkat' => 'success',
            'dalam_perjalanan' => 'warning',
            'selesai' => 'secondary',
        ];
    @endphp

    <div class="card">
        <div class="card-header">Daftar Jadwal</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>Sopir</th>
                        <th>Rute</th>
                        <th>Waktu Berangkat</th>
                        <th>Catatan Sopir</th>
                        <th>Status</th>
                        <th class="text-nowrap">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($jadwal as $item)
                        @php $badge = $badgeClass[$item->status] ?? 'secondary'; @endphp
                        <tr>
                            <td>{{ $item->sopir->nama ?? $item->sopir->user->name ?? '-' }}</td>
                            <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                            <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                            <td>{{ $item->catatan ?? '-' }}</td>
                            <td><span class="badge text-bg-{{ $badge }} text-capitalize">{{ str_replace('_', ' ', $item->status) }}</span></td>
                            <td>
                                @if($pesananPerJadwal->has($item->id))
                                    <span class="badge text-bg-success">Sudah dipesan</span>
                                @elseif($item->status === 'siap_berangkat')
                                    <form method="POST" action="{{ route('penumpang.pesan') }}" class="d-flex flex-column flex-lg-row gap-2">
                                        @csrf
                                        <input type="hidden" name="jadwal_id" value="{{ $item->id }}">
                                        <input type="text" name="catatan" class="form-control form-control-sm" placeholder="Catatan">
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Pesan</button>
                                    </form>
                                @else
                                    <span class="text-muted">Tidak tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada jadwal yang cocok dengan pencarian Anda.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
