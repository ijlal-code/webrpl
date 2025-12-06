@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Riwayat Pesanan</h1>
    <p class="text-muted">Pesanan selesai dan dibatalkan akan tampil di sini. Anda dapat menghapus riwayat yang tidak diperlukan.</p>

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

    @php
        $statusBadge = [
            'menunggu' => 'warning',
            'dikonfirmasi' => 'primary',
            'selesai' => 'success',
            'dibatalkan' => 'secondary',
        ];
    @endphp

    <div class="card">
        <div class="card-header">Daftar Riwayat</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>Rute</th>
                        <th>Jadwal</th>
                        <th>Sopir</th>
                        <th>Catatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($pesanan as $item)
                        <tr>
                            <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                            <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                            <td>{{ $item->sopir->nama ?? $item->jadwal->sopir->nama ?? '-' }}</td>
                            <td>{{ $item->catatan ?? '-' }}</td>
                            <td><span class="badge text-bg-{{ $statusBadge[$item->status] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $item->status) }}</span></td>
                            <td>
                                <form method="POST" action="{{ route('penumpang.riwayat.hapus', $item) }}" class="d-inline" onsubmit="return confirm('Hapus riwayat ini? Data tidak dapat dikembalikan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus Riwayat</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Belum ada riwayat. Pesanan yang selesai akan muncul di sini.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
