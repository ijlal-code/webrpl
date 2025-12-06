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

    {{-- Daftar pesanan juga ditempatkan di partial terpisah agar struktur rapi --}}
    @include('sopir.partials.pesanan')
</div>
@endsection
