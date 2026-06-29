@extends('layouts.app')

@section('title', 'Absensi')
@section('page-title', 'Halaman Absensi')

@section('content')
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="gpa-card">
                <div class="gpa-card-header">Form Absensi</div>
                <div class="gpa-card-body">
                    <div class="mb-3">
                        <label for="employee_id" class="form-label fw-medium">Nama Pegawai</label>
                        <select id="employee_id" class="form-select">
                            <option value="">Pilih Nama Pegawai</option>
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
                <div class="gpa-card-header">Kamera</div>
                <div class="gpa-card-body text-center">
                    <video id="camera_preview" class="gpa-camera-preview" autoplay playsinline muted></video>
                    <canvas id="photo_canvas" class="d-none"></canvas>
                    <p class="text-muted small mt-2">Kamera aktif otomatis saat halaman dibuka.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/attendance.js') }}"></script>
@endpush
