@php
    $statusBadge = [
        'menunggu' => 'warning',
        'dikonfirmasi' => 'primary',
        'selesai' => 'success',
        'dibatalkan' => 'secondary',
    ];
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center gap-3 flex-wrap">
        <div>
            <div class="fw-semibold">Pesanan Masuk</div>
            <small class="text-muted">Kelola pesanan penumpang yang terkait dengan jadwal Anda.</small>
        </div>
        @php
            $totalMenunggu = $pesanan->where('status', 'menunggu')->count();
            $totalDikonfirmasi = $pesanan->where('status', 'dikonfirmasi')->count();
        @endphp
        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
            <span class="badge text-bg-warning text-uppercase">Menunggu: {{ $totalMenunggu }}</span>
            <span class="badge text-bg-primary text-uppercase">Dikonfirmasi: {{ $totalDikonfirmasi }}</span>
            @if($totalMenunggu > 0)
                <form method="POST" action="{{ route('sopir.pesanan.konfirmasi.semua') }}" onsubmit="return confirm('Konfirmasi semua pesanan menunggu?');">
                    @csrf
                    <button class="btn btn-sm btn-success">Konfirmasi Semua</button>
                </form>
            @endif
            @if($totalDikonfirmasi > 0)
                <form method="POST" action="{{ route('sopir.pesanan.selesai.semua') }}" onsubmit="return confirm('Tandai semua pesanan terkonfirmasi sebagai selesai?');">
                    @csrf
                    <button class="btn btn-sm btn-outline-primary">Selesai Semua</button>
                </form>
            @endif
        </div>
    </div>
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
