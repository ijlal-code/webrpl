@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h1 class="mb-1">Pesanan</h1>
            <p class="text-muted mb-0">Cari pesanan dan hapus jika diperlukan.</p>
        </div>
        <form method="GET" action="{{ route('pesanan.index') }}" class="d-flex gap-2">
            <input type="search" name="q" value="{{ $keyword ?? '' }}" class="form-control" placeholder="Cari penumpang, sopir, atau rute">
            <button class="btn btn-outline-primary" type="submit">Cari</button>
        </form>
    </div>

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

    <div class="table-responsive shadow-sm rounded-3 bg-white">
        <table class="table table-hover mb-0 align-middle">
            <thead>
            <tr>
                <th>Penumpang</th>
                <th>Sopir</th>
                <th>Rute</th>
                <th>Jadwal</th>
                <th>Catatan Sopir</th>
                <th>Catatan Penumpang</th>
                <th>Status</th>
                <th>Alasan Pembatalan</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @foreach($pesanan as $item)
                <tr>
                    <td>{{ $item->penumpang->name ?? '-' }}</td>
                    <td>{{ $item->sopir->nama ?? $item->jadwal->sopir->nama ?? '-' }}</td>
                    <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                    <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                    <td>{{ $item->jadwal->catatan ?? '-' }}</td>
                    <td>{{ $item->catatan ?? '-' }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
                    <td>{{ $item->alasan_pembatalan ?? '-' }}</td>
                    <td>
                        <form method="POST" action="{{ route('pesanan.destroy', $item) }}" onsubmit="return confirm('Hapus pesanan ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
