<div class="gpa-empty-state text-center py-5">
    <div class="gpa-empty-icon mb-3">
        <i class="bi {{ $icon ?? 'bi-inbox' }}"></i>
    </div>
    <h6 class="fw-semibold text-dark mb-1">{{ $title ?? 'Tidak ada data' }}</h6>
    <p class="text-muted small mb-0">{{ $message ?? 'Belum ada data untuk ditampilkan.' }}</p>
    @isset($actionUrl)
        <a href="{{ $actionUrl }}" class="btn btn-gpa-primary btn-sm mt-3">{{ $actionLabel ?? 'Tambah Data' }}</a>
    @endisset
</div>
