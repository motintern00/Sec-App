@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi')

@section('content')
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
                            <th>Foto</th>
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
                                <td>
                                    @if($attendance->check_in_photo)
                                        <img src="{{ asset('storage/'.$attendance->check_in_photo) }}" alt="Foto" class="gpa-photo-thumb" onclick="showPhotoModal('{{ asset('storage/'.$attendance->check_in_photo) }}')">
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $attendances->links() }}</div>

    <div class="modal fade" id="photoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="photoModalImage" src="" alt="Foto Absensi" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function showPhotoModal(url) {
    document.getElementById('photoModalImage').src = url;
    new bootstrap.Modal(document.getElementById('photoModal')).show();
}
</script>
@endpush
