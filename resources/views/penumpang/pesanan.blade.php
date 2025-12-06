@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Pesanan Aktif</h1>
    <p class="text-muted">Pesanan yang sudah selesai akan otomatis hilang dari sini dan masuk ke riwayat.</p>

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

    <div class="card">
        <div class="card-header">Pesanan Menunggu / Dikonfirmasi</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
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
                        @php
                            $statusMapping = [
                                'menunggu' => ['label' => 'Menunggu', 'badge' => 'warning'],
                                'aktif' => ['label' => 'Aktif', 'badge' => 'success'],
                                'sedang_jalan' => ['label' => 'Sedang jalan', 'badge' => 'info'],
                                'tidak_aktif' => ['label' => 'Tidak aktif', 'badge' => 'secondary'],
                                'dikonfirmasi' => ['label' => 'Dikonfirmasi', 'badge' => 'primary'],
                            ];

                            $statusSopir = $item->jadwal->status ?? 'dikonfirmasi';
                            $statusUntukTampilan = $item->status === 'dikonfirmasi' ? $statusSopir : $item->status;
                            $badge = $statusMapping[$statusUntukTampilan]['badge'] ?? 'secondary';
                            $label = $statusMapping[$statusUntukTampilan]['label'] ?? str_replace('_', ' ', $statusUntukTampilan);
                        @endphp
                        <tr>
                            <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                            <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                            <td>{{ $item->sopir->nama ?? $item->jadwal->sopir->nama ?? '-' }}</td>
                            <td>{{ $item->catatan ?? '-' }}</td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge text-bg-{{ $badge }} text-capitalize">{{ $label }}</span>
                                    @if($item->status === 'dikonfirmasi')
                                        <small class="text-muted">Status sopir terkini</small>
                                    @else
                                        <small class="text-muted">Menunggu konfirmasi</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('penumpang.pesanan.batalkan', $item) }}" class="d-flex flex-column gap-2">
                                    @csrf
                                    <select name="alasan" class="form-select form-select-sm alasan-select" data-target="#alasan-lain-{{ $item->id }}">
                                        <option value="perubahan_rencana">Perubahan rencana perjalanan</option>
                                        <option value="menemukan_transportasi_lain">Menemukan transportasi lain</option>
                                        <option value="kesalahan_pemesanan">Kesalahan pemesanan</option>
                                        <option value="lainnya">Alasan lainnya</option>
                                    </select>
                                    <input type="text" name="alasan_lain" id="alasan-lain-{{ $item->id }}" class="form-control form-control-sm alasan-lain" placeholder="Tuliskan alasan lain" style="display: none;">
                                    <button class="btn btn-sm btn-danger" type="submit">Batalkan Pesanan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Belum ada pesanan aktif. <a href="{{ route('penumpang.jadwal') }}">Pesan jadwal sekarang</a>.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.alasan-select').forEach(select => {
            const target = document.querySelector(select.dataset.target);

            const toggleInput = () => {
                if (!target) return;
                target.style.display = select.value === 'lainnya' ? 'block' : 'none';
            };

            select.addEventListener('change', toggleInput);
            toggleInput();
        });
    });
</script>
@endsection
