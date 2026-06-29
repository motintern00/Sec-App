@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Admin')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="gpa-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="gpa-stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="gpa-stat-value">{{ $stats['total_employees'] }}</div>
                        <div class="gpa-stat-label">Total Pegawai</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="gpa-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="gpa-stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="gpa-stat-value">{{ $stats['hadir_hari_ini'] }}</div>
                        <div class="gpa-stat-label">Hadir Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="gpa-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="gpa-stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div>
                        <div class="gpa-stat-value">{{ $stats['terlambat'] }}</div>
                        <div class="gpa-stat-label">Terlambat</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="gpa-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="gpa-stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div>
                        <div class="gpa-stat-value">{{ $stats['belum_hadir'] }}</div>
                        <div class="gpa-stat-label">Belum Hadir</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="gpa-card">
        <div class="gpa-card-header d-flex align-items-center justify-content-between">
            <span><i class="bi bi-list-check me-2"></i>Riwayat Absensi Hari Ini</span>
            <span class="badge bg-primary">{{ now()->format('d M Y') }}</span>
        </div>
        <div class="gpa-card-body p-0">
            <div class="table-responsive">
                <table class="table table-gpa table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Pegawai</th>
                            <th>Departemen</th>
                            <th>Shift</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayAttendances as $attendance)
                            <tr>
                                <td class="fw-medium">{{ $attendance->employee->name }}</td>
                                <td>{{ $attendance->employee->department->name }}</td>
                                <td><span class="badge bg-secondary">{{ $attendance->employee->shift->name }}</span></td>
                                <td>{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td>
                                <td>{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $attendance->status->badgeClass() }}">
                                        {{ $attendance->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data absensi hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
