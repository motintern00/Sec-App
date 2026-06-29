@extends('layouts.app')

@section('title', 'Data Absensi')
@section('page-title', 'Data Absensi')

@section('content')
    <div class="gpa-card mb-4">
        <div class="gpa-card-header">Filter Data</div>
        <div class="gpa-card-body">
            <form method="GET" action="{{ route('admin.attendances.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nama Pegawai</label>
                    <select name="employee_id" class="form-select">
                        <option value="">Semua Pegawai</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cari Nama</label>
                    <input type="text" name="name" class="form-control" placeholder="Ketik nama..." value="{{ request('name') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-gpa-primary"><i class="bi bi-search me-1"></i> Filter</button>
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="gpa-card">
        <div class="gpa-card-body p-0">
            <div class="table-responsive">
                <table class="table table-gpa table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pegawai</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                            <th class="text-end">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->attendance_date->format('d/m/Y') }}</td>
                                <td class="fw-medium">{{ $attendance->employee->name }}</td>
                                <td>{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td>
                                <td>{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $attendance->status->badgeClass() }}">{{ $attendance->status->label() }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.attendances.show', $attendance) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Tidak ada data absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $attendances->links() }}</div>
@endsection
