@extends('layouts.app')

@section('title', 'Detail Absensi')
@section('page-title', 'Detail Absensi')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="gpa-card">
                <div class="gpa-card-header">Informasi Absensi</div>
                <div class="gpa-card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th width="40%">Pegawai</th><td>{{ $attendance->employee->name }}</td></tr>
                        <tr><th>Departemen</th><td>{{ $attendance->employee->department->name }}</td></tr>
                        <tr><th>Shift</th><td>{{ $attendance->employee->shift->name }}</td></tr>
                        <tr><th>Tanggal</th><td>{{ $attendance->attendance_date->format('d F Y') }}</td></tr>
                        <tr><th>Jam Masuk</th><td>{{ $attendance->check_in_at?->format('H:i:s') ?? '-' }}</td></tr>
                        <tr><th>Jam Pulang</th><td>{{ $attendance->check_out_at?->format('H:i:s') ?? '-' }}</td></tr>
                        <tr><th>Status</th><td><span class="badge {{ $attendance->status->badgeClass() }}">{{ $attendance->status->label() }}</span></td></tr>
                        <tr><th>Petugas</th><td>{{ $attendance->recorder?->name ?? '-' }}</td></tr>
                        <tr><th>Koordinat Masuk</th><td>{{ $attendance->check_in_latitude }}, {{ $attendance->check_in_longitude }}</td></tr>
                        <tr><th>Koordinat Pulang</th><td>{{ $attendance->check_out_latitude ? $attendance->check_out_latitude.', '.$attendance->check_out_longitude : '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="gpa-card mb-3">
                <div class="gpa-card-header">Foto Check-in</div>
                <div class="gpa-card-body text-center">
                    @if($attendance->check_in_photo)
                        <img src="{{ asset('storage/'.$attendance->check_in_photo) }}" alt="Foto Check-in" class="img-fluid rounded" style="max-height: 280px;">
                    @else
                        <p class="text-muted">Tidak ada foto</p>
                    @endif
                </div>
            </div>
            <div class="gpa-card">
                <div class="gpa-card-header">Foto Check-out</div>
                <div class="gpa-card-body text-center">
                    @if($attendance->check_out_photo)
                        <img src="{{ asset('storage/'.$attendance->check_out_photo) }}" alt="Foto Check-out" class="img-fluid rounded" style="max-height: 280px;">
                    @else
                        <p class="text-muted">Tidak ada foto</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
