@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')

@section('breadcrumb')
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Log Aktivitas'],
    ]])
@endsection

@section('content')
    <div class="gpa-card mb-4">
        <div class="gpa-card-header">Filter Log</div>
        <div class="gpa-card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Aksi</label>
                    <select name="action" class="form-select">
                        <option value="">Semua</option>
                        <option value="check_in" @selected(request('action') === 'check_in')>Check-in</option>
                        <option value="check_out" @selected(request('action') === 'check_out')>Check-out</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-gpa-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="gpa-card">
        <div class="gpa-card-body p-0">
            @if($logs->isEmpty())
                @include('components.empty-state', [
                    'icon' => 'bi-journal-x',
                    'title' => 'Belum ada log',
                    'message' => 'Aktivitas absensi akan tercatat otomatis di sini.',
                ])
            @else
                <div class="table-responsive">
                    <table class="table table-gpa table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Petugas</th>
                                <th>Aksi</th>
                                <th>Pegawai</th>
                                <th>IP Address</th>
                                <th>Perangkat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $log->user->name }}</td>
                                    <td>
                                        <span class="badge {{ $log->action === 'check_in' ? 'bg-success' : 'bg-primary' }}">
                                            {{ $log->actionLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $log->employee?->name ?? '-' }}</td>
                                    <td><code>{{ $log->ip_address ?? '-' }}</code></td>
                                    <td class="small text-muted text-truncate" style="max-width: 200px;">{{ \Illuminate\Support\Str::limit($log->user_agent, 60) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">{{ $logs->links() }}</div>
@endsection
