@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun dan password')

@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="gpa-card">
            <div class="gpa-card-header">Informasi Akun</div>
            <div class="gpa-card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @if($user->employee)
                        <div class="mb-3">
                            <label class="form-label">Shift</label>
                            <input type="text" class="form-control" value="{{ $user->employee->shift->name }}" readonly disabled>
                        </div>
                    @endif
                    <button type="submit" class="btn btn-gpa-primary">Simpan Profil</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="gpa-card">
            <div class="gpa-card-header">Ganti Password</div>
            <div class="gpa-card-body">
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-gpa-primary">Ubah Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
