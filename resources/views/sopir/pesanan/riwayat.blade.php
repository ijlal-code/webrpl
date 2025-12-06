@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-1">Riwayat Pesanan</h1>
    <p class="text-muted mb-4">Pesanan yang selesai atau dibatalkan tetap tercatat di sini dan bisa dibersihkan jika perlu.</p>

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
            'selesai' => 'success',
            'dibatalkan' => 'secondary',
        ];
    @endphp

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar Riwayat</span>
            @if($pesanan->isNotEmpty())
                <form method="POST" action="{{ route('sopir.pesanan.riwayat.bersihkan') }}" onsubmit="return confirm('Bersihkan semua riwayat pesanan?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">Bersihkan Riwayat</button>
                </form>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Penumpang</th>
                            <th>Rute</th>
                            <th>Jadwal</th>
                            <th>Catatan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesanan as $item)
                            <tr>
                                <td>{{ $item->penumpang->name ?? '-' }}</td>
                                <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                                <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                                <td>{{ $item->catatan ?? '-' }}</td>
                                <td><span class="badge text-bg-{{ $statusBadge[$item->status] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $item->status) }}</span></td>
                                <td>
                                    <form method="POST" action="{{ route('sopir.pesanan.hapus', $item) }}" onsubmit="return confirm('Hapus riwayat ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Belum ada riwayat pesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
