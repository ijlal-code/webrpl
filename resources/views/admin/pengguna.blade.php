@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h1 class="mb-1">Profil Pengguna</h1>
            <p class="text-muted mb-0">Lihat detail penumpang dan sopir lalu hapus akun bila diperlukan.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">Penumpang</div>
                <div class="card-body">
                    @forelse($penumpang as $user)
                        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                            <div>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <div class="text-muted small">{{ $user->email }}</div>
                                <div class="mt-2">
                                    <div>Alamat: {{ $user->profil->alamat ?? '-' }}</div>
                                    <div>Nomor HP: {{ $user->profil->nomor_hp ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <a class="btn btn-sm btn-outline-secondary mb-2" href="{{ route('profil.public', $user) }}" target="_blank">Lihat Profil</a>
                                <form method="POST" action="{{ route('admin.pengguna.hapus', $user) }}" onsubmit="return confirm('Hapus akun penumpang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada penumpang.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">Sopir</div>
                <div class="card-body">
                    @forelse($sopir as $user)
                        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                            <div>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <div class="text-muted small">{{ $user->email }}</div>
                                <div class="mt-2">
                                    <div>Nomor SIM: {{ $user->sopir->nomor_sim ?? '-' }}</div>
                                    <div>Nomor HP: {{ $user->profil->nomor_hp ?? '-' }}</div>
                                    <div>Alamat: {{ $user->profil->alamat ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <a class="btn btn-sm btn-outline-secondary mb-2" href="{{ route('profil.public', $user) }}" target="_blank">Lihat Profil</a>
                                <form method="POST" action="{{ route('admin.pengguna.hapus', $user) }}" onsubmit="return confirm('Hapus akun sopir ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada sopir.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
