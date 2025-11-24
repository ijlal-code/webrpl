@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h4 class="mb-0">📝 Buat Profil</h4>
                </div>
                <div class="card-body">

                    <form action="{{ route('profil.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" name="alamat" id="alamat" class="form-control rounded-3"
                                value="{{ old('alamat', auth()->user()->profil->alamat ?? '') }}"
                                placeholder="Contoh: Jl. Merdeka No.10, Kecamatan ABC">
                        </div>

                        <div class="mb-3">
                            <label for="nomor_hp" class="form-label">Nomor HP</label>
                            <input type="text" name="nomor_hp" id="nomor_hp" class="form-control rounded-3"
                                value="{{ old('nomor_hp', auth()->user()->profil->nomor_hp ?? '') }}"
                                placeholder="Contoh: 081234567890">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left-circle"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
