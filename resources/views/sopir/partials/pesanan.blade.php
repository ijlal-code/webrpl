@php
    $statusBadge = [
        'menunggu' => 'warning',
        'dikonfirmasi' => 'primary',
        'selesai' => 'success',
        'dibatalkan' => 'secondary',
    ];
@endphp

<div class="card">
    <div class="card-header">Pesanan Masuk</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>Penumpang</th>
                    <th>Rute</th>
                    <th>Jadwal</th>
                    <th>Catatan Penumpang</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($pesanan as $item)
                    <tr>
                        <td>
                            @if($item->penumpang)
                                <a href="{{ route('profil.public', $item->penumpang) }}" class="text-decoration-none">{{ $item->penumpang->name }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                        <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                        <td>{{ $item->catatan ?? '-' }}</td>
                        <td><span class="badge text-bg-{{ $statusBadge[$item->status] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $item->status) }}</span></td>
                        <td>
                            @if($item->status === 'menunggu')
                                <form method="POST" action="{{ route('sopir.pesanan.konfirmasi', $item) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Konfirmasi</button>
                                </form>
                            @elseif($item->status === 'dikonfirmasi')
                                <form method="POST" action="{{ route('sopir.pesanan.selesai', $item) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-primary">Tandai Selesai</button>
                                </form>
                            @elseif($item->status === 'selesai')
                                <form method="POST" action="{{ route('sopir.pesanan.hapus', $item) }}" class="d-inline" onsubmit="return confirm('Hapus pesanan selesai ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            @else
                                <span class="text-muted">Tidak ada aksi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Belum ada pesanan yang masuk.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
