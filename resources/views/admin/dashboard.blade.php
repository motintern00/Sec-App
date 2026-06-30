@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Admin')

@section('breadcrumb')
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Ringkasan Hari Ini'],
    ]])
@endsection

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

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="gpa-card gpa-chart-card">
                <div class="gpa-card-header">Grafik Kehadiran 7 Hari Terakhir</div>
                <div class="gpa-card-body">
                    <canvas id="chart7Days" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="gpa-card gpa-chart-card">
                <div class="gpa-card-header">Grafik Kehadiran 30 Hari Terakhir</div>
                <div class="gpa-card-body">
                    <canvas id="chart30Days" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="gpa-card">
                <div class="gpa-card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-person-x me-2"></i>Belum Hadir Hari Ini</span>
                    <span class="badge bg-danger">{{ $absentEmployees->count() }}</span>
                </div>
                <div class="gpa-card-body p-0">
                    @if($absentEmployees->isEmpty())
                        @include('components.empty-state', [
                            'icon' => 'bi-emoji-smile',
                            'title' => 'Semua pegawai sudah hadir',
                            'message' => 'Tidak ada pegawai yang belum absen hari ini.',
                        ])
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($absentEmployees as $employee)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-medium">{{ $employee->name }}</div>
                                        <small class="text-muted">{{ $employee->shift->name }}</small>
                                    </div>
                                    <span class="badge bg-danger-subtle text-danger">Belum Hadir</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="gpa-card">
                <div class="gpa-card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-list-check me-2"></i>Riwayat Absensi Hari Ini</span>
                    <span class="badge bg-primary">{{ now()->format('d M Y') }}</span>
                </div>
                <div class="gpa-card-body p-0">
                    @if($todayAttendances->isEmpty())
                        @include('components.empty-state', [
                            'icon' => 'bi-calendar-x',
                            'title' => 'Belum ada absensi',
                            'message' => 'Data absensi hari ini akan muncul di sini.',
                        ])
                    @else
                        <div class="table-responsive">
                            <table class="table table-gpa table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Pegawai</th>
                                        <th>Shift</th>
                                        <th>Masuk</th>
                                        <th>Pulang</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayAttendances as $attendance)
                                        <tr>
                                            <td class="fw-medium">{{ $attendance->employee->name }}</td>
                                            <td><span class="badge bg-secondary">{{ $attendance->employee->shift->name }}</span></td>
                                            <td>{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td>
                                            <td>{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td>
                                            <td><span class="badge {{ $attendance->status->badgeClass() }}">{{ $attendance->status->label() }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartDefaults = {
        type: 'bar',
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    };

    new Chart(document.getElementById('chart7Days'), {
        ...chartDefaults,
        data: {
            labels: @json($chart7Days['labels']),
            datasets: [
                { label: 'Tepat Waktu', data: @json($chart7Days['tepatWaktu']), backgroundColor: '#22c55e' },
                { label: 'Terlambat', data: @json($chart7Days['terlambat']), backgroundColor: '#f59e0b' },
                { label: 'Belum Hadir', data: @json($chart7Days['belumHadir']), backgroundColor: '#ef4444' },
            ]
        }
    });

    new Chart(document.getElementById('chart30Days'), {
        ...chartDefaults,
        data: {
            labels: @json($chart30Days['labels']),
            datasets: [
                { label: 'Tepat Waktu', data: @json($chart30Days['tepatWaktu']), backgroundColor: '#22c55e' },
                { label: 'Terlambat', data: @json($chart30Days['terlambat']), backgroundColor: '#f59e0b' },
                { label: 'Belum Hadir', data: @json($chart30Days['belumHadir']), backgroundColor: '#64748b' },
            ]
        }
    });
});
</script>
@endpush
