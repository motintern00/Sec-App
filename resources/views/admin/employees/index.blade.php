@extends('layouts.app')

@section('title', 'Master Pegawai')
@section('page-title', 'Master Pegawai')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Kelola data pegawai Security GPA</p>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-gpa-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Pegawai
        </a>
    </div>

    <div class="gpa-card">
        <div class="gpa-card-body p-0">
            <div class="table-responsive">
                <table class="table table-gpa table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>Shift</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td class="fw-medium">{{ $employee->name }}</td>
                                <td>{{ $employee->department->name }}</td>
                                <td><span class="badge bg-primary">{{ $employee->shift->name }}</span></td>
                                <td>
                                    @if($employee->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="d-inline" id="delete-form-{{ $employee->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="GpaApp.confirmDelete(document.getElementById('delete-form-{{ $employee->id }}'))">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data pegawai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $employees->links() }}
    </div>
@endsection
