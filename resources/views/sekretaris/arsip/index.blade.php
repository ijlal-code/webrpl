@extends('layouts.app')

@section('content')
<div class="py-5" style="min-height: 100vh; background: #f3f4f6;">
    <div class="container">

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary">📂 Daftar Arsip</h2>
                <p class="text-muted mb-0">Kelola, cari, dan akses arsip yang tersedia</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark fw-semibold">
                <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Dashboard
            </a>
        </div>

        {{-- Form Pencarian --}}
        <form action="{{ route('sekretaris.arsip.index') }}" method="GET" class="row g-2 align-items-center mb-4">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="🔍 Cari judul arsip...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary fw-semibold">
                    <i class="bi bi-search"></i> Cari
                </button>
                <a href="{{ route('sekretaris.arsip.index') }}" class="btn btn-secondary fw-semibold">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
            <div class="col text-end">
                <a href="{{ route('sekretaris.arsip.create') }}" class="btn btn-success fw-semibold">
                    <i class="bi bi-cloud-arrow-up"></i> Upload Arsip Baru
                </a>
            </div>
        </form>

        {{-- Tabel Arsip --}}
        <div class="table-responsive shadow-sm">
            <table class="table table-hover table-bordered bg-white rounded overflow-hidden">
                <thead class="table-light text-center align-middle">
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Uploader</th>
                        <th>Tanggal Upload</th>
                        <th>File</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($arsip as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->judul_arsip }}</td>
                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $item->user->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_upload)->format('d-m-Y') }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $item->file_arsip }}</span>
                            </td>
                            <td class="text-center">
                            @if (auth()->user()->role === 'sekretaris' || auth()->user()->role === 'admin')
                                <a href="{{ route('arsip.show', $item->id) }}" class="btn btn-sm btn-info mb-1">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                 @endif

                                @if (auth()->user()->role === 'kepala')
                                    <a href="{{ asset('storage/' . $item->file_arsip) }}" target="_blank" class="btn btn-sm btn-success mb-1">
                                        <i class="bi bi-download"></i> Unduh
                                    </a>
                                @endif

                                @if (auth()->user()->role === 'sekretaris' || auth()->user()->role === 'admin')
                                    <a href="{{ route('arsip.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('arsip.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus arsip ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger mb-1">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada arsip.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
