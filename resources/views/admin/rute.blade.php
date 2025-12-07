@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <h1 class="mb-1">Rute Sopir</h1>
            <p class="text-muted mb-0">Daftar rute yang pernah dibuat oleh sopir.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive shadow-sm rounded-3 bg-white">
        <table class="table table-hover mb-0 align-middle">
            <thead>
            <tr>
                <th>Nama Rute</th>
                <th>Asal</th>
                <th>Tujuan</th>
                <th>Dipakai Jadwal</th>
                <th>Total Pesanan</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rute as $item)
                <tr>
                    <td>{{ $item->nama_rute }}</td>
                    <td>{{ $item->asal }}</td>
                    <td>{{ $item->tujuan }}</td>
                    <td>{{ $item->jadwal_count }}</td>
                    <td>{{ $item->pesanans_count }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.rute.hapus', $item) }}" onsubmit="return confirm('Hapus rute ini? Semua jadwal dan pesanan terkait juga akan ikut terhapus.');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Belum ada rute yang tercatat.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
