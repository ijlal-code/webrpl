@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h4 class="mb-0">👤 Profil Saya</h4>
                </div>
                <div class="card-body">
                @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item">
                            <i class="bi bi-person-fill text-primary me-2"></i>
                            <strong>Nama:</strong> {{ auth()->user()->name }}
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-envelope-fill text-danger me-2"></i>
                            <strong>Email:</strong> {{ auth()->user()->email }}
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-geo-alt-fill text-warning me-2"></i>
                            <strong>Alamat:</strong> {{ auth()->user()->profil->alamat ?? '-' }}
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-telephone-fill text-success me-2"></i>
                            <strong>Nomor HP:</strong> {{ auth()->user()->profil->nomor_hp ?? '-' }}
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Kembali
                        </a>
                        <a href="{{ route('profil.edit') }}" class="btn btn-warning text-white">
                            <i class="bi bi-pencil-square"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
