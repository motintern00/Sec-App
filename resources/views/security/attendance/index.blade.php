@extends('layouts.app')

@section('title', 'Absensi')
@section('page-title', 'Halaman Absensi')

@section('breadcrumb')
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Absensi', 'url' => route('security.attendance.index')],
        ['label' => 'Check-in / Check-out'],
    ]])
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="gpa-card">
                <div class="gpa-card-header">Form Absensi</div>
                <div class="gpa-card-body">
                    <div class="mb-3">
                        <label for="employee_id" class="form-label fw-medium">Nama Pegawai</label>
                        <select id="employee_id" class="form-select">
                            <option value="">Pilih atau ketik nama pegawai...</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Departemen</label>
                        <input type="text" id="department_name" class="form-control" readonly placeholder="Otomatis setelah memilih pegawai">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Informasi Shift</label>
                        <div id="shift_info" class="small text-muted">Pilih pegawai untuk melihat shift.</div>
                    </div>
                    <div class="mb-3">
                        <div id="geo_status" class="gpa-geo-status pending">
                            <i class="bi bi-geo-alt me-1"></i> Mendeteksi lokasi...
                        </div>
                    </div>
                    <div class="mb-3">
                        <div id="status_message" class="alert alert-info small">Pilih pegawai untuk memulai absensi.</div>
                    </div>
                    <button type="button" id="btn_attendance" class="btn btn-gpa-primary w-100 py-2" disabled>
                        <i class="bi bi-fingerprint me-1"></i> Absen Masuk
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="gpa-card">
                <div class="gpa-card-header d-flex justify-content-between align-items-center">
                    <span>Kamera</span>
                    <button type="button" id="btn_switch_camera" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-camera-reverse me-1"></i> Ganti Kamera
                    </button>
                </div>
                <div class="gpa-card-body text-center">
                    <video id="camera_preview" class="gpa-camera-preview" autoplay playsinline muted></video>
                    <canvas id="photo_canvas" class="d-none"></canvas>
                    <p class="text-muted small mt-2">Kamera aktif otomatis. Foto akan ditampilkan untuk konfirmasi sebelum absen.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/js/attendance.js') }}"></script>
@endpush
