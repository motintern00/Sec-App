@extends('layouts.app')

@section('title', 'Edit Pegawai')
@section('page-title', 'Edit Pegawai')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="gpa-card">
                <div class="gpa-card-header">Edit Pegawai — {{ $employee->name }}</div>
                <div class="gpa-card-body">
                    <form action="{{ route('admin.employees.update', $employee) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="department_id" class="form-label">Departemen</label>
                            <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" @selected(old('department_id', $employee->department_id) == $department->id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="shift_id" class="form-label">Shift</label>
                            <select name="shift_id" id="shift_id" class="form-select @error('shift_id') is-invalid @enderror" required>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" @selected(old('shift_id', $employee->shift_id) == $shift->id)>{{ $shift->name }}</option>
                                @endforeach
                            </select>
                            @error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" @checked(old('is_active', $employee->is_active))>
                            <label class="form-check-label" for="is_active">Pegawai Aktif</label>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-gpa-primary">Perbarui</button>
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
