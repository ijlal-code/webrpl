@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3>Edit Kategori Arsip</h3>

    <form action="{{ route('sekretaris.kategori.update', $kategori->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nama_kategori" class="form-label">Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" value="{{ $kategori->nama_kategori }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('sekretaris.kategori.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
