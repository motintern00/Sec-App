@extends('layouts.app')

@section('title', 'Absensi')
@section('page-title', 'Absensi')
@section('page-subtitle', 'Check-in dan check-out harian Anda')

@section('breadcrumb')
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Absensi', 'url' => route('employee.attendance.index')],
        ['label' => 'Hari Ini'],
    ]])
@endsection

@section('content')
<div class="gpa-attendance-page">
    <div class="gpa-employee-hero d-flex align-items-center gap-3">
        <div class="gpa-employee-avatar">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
        <div>
            <div class="h5 mb-0 fw-bold">{{ $employee->name }}</div>
            <div class="small opacity-75">{{ $employee->department->name }} · {{ $employee->shift->name }}</div>
            <div class="small opacity-75">Masuk {{ substr($employee->shift->check_in_time, 0, 5) }} · Toleransi {{ substr($employee->shift->tolerance_time, 0, 5) }}</div>
        </div>
    </div>

    <div class="gpa-step-indicator">
        <div class="gpa-step" id="step_gps"><i class="bi bi-geo-alt"></i> GPS</div>
        <div class="gpa-step" id="step_camera"><i class="bi bi-camera"></i> Kamera</div>
        <div class="gpa-step active" id="step_absen"><i class="bi bi-fingerprint"></i> Absen</div>
    </div>

    <div class="row g-3">
        <div class="col-12 order-1 order-lg-2">
            <div class="gpa-card">
                <div class="gpa-card-header d-flex justify-content-between align-items-center">
                    <span>Kamera</span>
                    <button type="button" id="btn_switch_camera" class="btn btn-sm btn-outline-light text-dark border">
                        <i class="bi bi-camera-reverse"></i> Ganti
                    </button>
                </div>
                <div class="gpa-card-body p-2 text-center">
                    <video id="camera_preview" class="gpa-camera-preview" autoplay playsinline muted></video>
                    <canvas id="photo_canvas" class="d-none"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 order-2 order-lg-1">
            <div class="gpa-card">
                <div class="gpa-card-body">
                    <div class="mb-3" id="geo_status" class="gpa-geo-status pending">
                        <i class="bi bi-geo-alt me-1"></i> Mendeteksi lokasi...
                    </div>
                    <div id="status_message" class="alert alert-secondary small mb-3">{{ $payload['status_message'] }}</div>
                    <div class="gpa-sticky-action">
                        <button type="button" id="btn_attendance" class="btn btn-gpa-primary w-100 py-3 fs-6" @if(!$payload['can_check_in'] && !$payload['can_check_out']) disabled @endif>
                            <i class="bi bi-fingerprint me-1"></i> {{ $payload['action_label'] }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>window.gpaEmployeePayload = @json($payload);</script>
<script src="{{ asset('assets/js/attendance.js') }}"></script>
@endpush
