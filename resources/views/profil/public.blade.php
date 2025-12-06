@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded-4">
                <div class="card-header bg-info text-white rounded-top-4">
                    <h4 class="mb-0">Profil Pengguna</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-person-fill text-primary me-2"></i>Nama</span>
                            <strong>{{ $profilUser->name }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-envelope-fill text-danger me-2"></i>Email</span>
                            <strong>{{ $profilUser->email }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-geo-alt-fill text-warning me-2"></i>Alamat</span>
                            <span>{{ $profilUser->profil->alamat ?? 'Belum diisi' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-telephone-fill text-success me-2"></i>Nomor HP</span>
                            <span>{{ $profilUser->profil->nomor_hp ?? 'Belum diisi' }}</span>
                        </li>
                    </ul>

                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
