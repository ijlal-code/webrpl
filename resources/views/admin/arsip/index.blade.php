@extends('layouts.app')

@section('content')
<div class="py-5" style="min-height: 100vh; background-color: #f8f9fa;">
    <div class="container">

        {{-- Alert sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Judul dan Aksi --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">📂 Daftar Arsip</h2>
                <small class="text-muted">Lihat dan kelola arsip yang telah diunggah</small>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark">
                <i class="bi bi-arrow-left-circle me-1"></i> Dashboard
            </a>
        </div>

        {{-- Form Pencarian --}}
        <form action="{{ route('admin.arsip.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="🔍 Cari judul arsip...">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>
                <a href="{{ route('admin.arsip.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
            
        </form>

        {{-- Tabel Arsip --}}
        <div class="table-responsive shadow-sm">
            <table class="table table-hover table-bordered bg-white">
                <thead class="table-light text-center">
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Uploader</th>
                        <th>Tanggal Upload</th>
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
                            <td class="text-center">
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('admin.arsip.show', $item->id) }}" class="btn btn-sm btn-info mb-1">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                 @endif

                                @if (auth()->user()->role === 'kepala')
                                    <a href="{{ asset('storage/' . $item->file_arsip) }}" target="_blank" class="btn btn-sm btn-success mb-1">
                                        <i class="bi bi-download"></i> Unduh
                                    </a>
                                @endif

                                @if (auth()->user()->role === 'sekretaris' || auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.arsip.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.arsip.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus arsip ini?')">
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
                            <td colspan="7" class="text-center text-muted">Belum ada arsip tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
