@extends('layouts.app')

@section('content')
<div class="py-5" style="min-height: 100vh; background-color: #f8f9fa;">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h3 class="mb-4">📁 Tambah Kategori Arsip</h3>

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <form action="{{ route('sekretaris.kategori.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="nama_kategori"
                                   class="form-control rounded-3 @error('nama_kategori') is-invalid @enderror"
                                   placeholder="Contoh: Surat Masuk" value="{{ old('nama_kategori') }}" required>

                            @error('nama_kategori')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sekretaris.kategori.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left-circle me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-folder-plus me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
