@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-1">Dashboard Sopir</h1>
    <p class="text-muted mb-4">Kelola jadwal keberangkatan Mobil Majene dan konfirmasi pesanan penumpang.</p>

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

    {{-- Bagian jadwal dipisah ke partial untuk memudahkan perawatan UI --}}
    @include('sopir.partials.jadwal')

    <div class="card mt-4">
        <div class="card-header">Aksi Cepat</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h5>Kelola Jadwal</h5>
                        <p class="text-muted mb-3">Lihat dan perbarui jadwal keberangkatan Anda secara lengkap.</p>
                        <a href="{{ route('sopir.jadwal.index') }}" class="btn btn-outline-primary w-100">Buka Jadwal</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h5>Pesanan Aktif</h5>
                        <p class="text-muted mb-3">Pantau pesanan yang sedang menunggu atau sudah dikonfirmasi.</p>
                        <a href="{{ route('sopir.pesanan.index') }}" class="btn btn-outline-primary w-100">Lihat Pesanan</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h5>Riwayat Pesanan</h5>
                        <p class="text-muted mb-3">Cek pesanan yang telah selesai atau dibatalkan dan kelola riwayatnya.</p>
                        <a href="{{ route('sopir.pesanan.riwayat') }}" class="btn btn-outline-primary w-100">Buka Riwayat</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
