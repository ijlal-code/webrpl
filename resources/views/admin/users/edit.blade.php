@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-warning text-white rounded-top-4">
            <h4><i class="bi bi-pencil-square"></i> Edit Pengguna</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Nama</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                        value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
    <label for="password" class="form-label fw-bold">Password (Kosongkan jika tidak diubah)</label>
    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
    @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


                <div class="mb-4">
                    <label for="role" class="form-label fw-bold">Role</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="sekretaris" {{ $user->role === 'sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                        <option value="kepala" {{ $user->role === 'kepala' ? 'selected' : '' }}>Kepala Desa</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
