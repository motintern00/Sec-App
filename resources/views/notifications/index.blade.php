@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
    <div class="gpa-card">
        <div class="gpa-card-header d-flex justify-content-between">
            <span>Semua Notifikasi</span>
            <form method="POST" action="{{ route('notifications.read-all') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">Tandai semua dibaca</button>
            </form>
        </div>
        <div class="gpa-card-body p-0">
            @forelse($notifications as $notification)
                <div class="gpa-notif-item {{ $notification->read_at ? '' : 'bg-light' }}">
                    <div class="d-flex gap-2">
                        <i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }} text-{{ $notification->data['color'] ?? 'primary' }} fs-5"></i>
                        <div>
                            <div class="fw-semibold">{{ $notification->data['title'] ?? 'Notifikasi' }}</div>
                            <div class="text-muted">{{ $notification->data['message'] ?? '' }}</div>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            @empty
                @include('components.empty-state', ['icon' => 'bi-bell-slash', 'title' => 'Tidak ada notifikasi', 'message' => 'Notifikasi absensi akan muncul di sini.'])
            @endforelse
        </div>
    </div>
    <div class="mt-3">{{ $notifications->links() }}</div>
@endsection
