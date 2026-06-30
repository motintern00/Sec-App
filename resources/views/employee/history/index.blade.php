@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi Saya')
@section('page-subtitle', 'Data absensi pribadi Anda')

@section('content')
    <div class="gpa-card">
        <div class="gpa-card-body p-0">
            @if($attendances->isEmpty())
                @include('components.empty-state', ['icon' => 'bi-calendar-x', 'title' => 'Belum ada riwayat', 'message' => 'Riwayat absensi Anda akan muncul di sini.'])
            @else
                <div class="table-responsive">
                    <table class="table table-gpa table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th>
                                <th>Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date->format('d/m/Y') }}</td>
                                    <td>{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td>
                                    <td>{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td>
                                    <td><span class="badge {{ $attendance->status->badgeClass() }}">{{ $attendance->status->label() }}</span></td>
                                    <td>
                                        @if($attendance->check_in_photo)
                                            <img src="{{ asset('storage/'.$attendance->check_in_photo) }}" class="gpa-photo-thumb" style="width:40px;height:40px;object-fit:cover;border-radius:6px" alt="Foto" onclick="window.open(this.src)">
                                        @else - @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <div class="mt-3">{{ $attendances->links() }}</div>
@endsection
