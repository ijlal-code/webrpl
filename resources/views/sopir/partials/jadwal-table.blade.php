@php
    $badgeClass = [
        'siap_berangkat' => 'success',
        'dalam_perjalanan' => 'warning',
        'selesai' => 'secondary',
    ];
@endphp

<div class="card h-100" id="jadwal-saya">
    <div class="card-header">Jadwal Saya</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>Rute</th>
                    <th>Keberangkatan</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th class="text-nowrap">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($jadwal as $item)
                    @php $badge = $badgeClass[$item->status] ?? 'secondary'; @endphp
                    <tr>
                        <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                        <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                        <td><span class="badge text-bg-{{ $badge }} text-capitalize">{{ str_replace('_', ' ', $item->status) }}</span></td>
                        <td>{{ $item->catatan ?? '-' }}</td>
                        <td>
                            <a href="{{ route('sopir.jadwal.edit', $item) }}" class="btn btn-sm btn-outline-secondary mb-2 w-100">Edit</a>
                            <form method="POST" action="{{ route('sopir.jadwal.update', $item) }}" class="d-flex flex-column gap-2">
                                @csrf
                                @method('PATCH')
                                <div class="d-flex gap-2 flex-wrap">
                                    <select name="status" class="form-select form-select-sm flex-grow-1">
                                        <option value="siap_berangkat" @selected($item->status === 'siap_berangkat')>Siap Berangkat</option>
                                        <option value="dalam_perjalanan" @selected($item->status === 'dalam_perjalanan')>Dalam Perjalanan</option>
                                        <option value="selesai" @selected($item->status === 'selesai')>Selesai</option>
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary" type="submit">Simpan</button>
                                </div>
                                <small class="text-muted">Perbarui catatan dari halaman edit jadwal.</small>
                            </form>
                            <form method="POST" action="{{ route('sopir.jadwal.destroy', $item) }}" class="mt-2" onsubmit="return confirm('Hapus jadwal ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger w-100" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Belum ada jadwal yang dibuat.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
