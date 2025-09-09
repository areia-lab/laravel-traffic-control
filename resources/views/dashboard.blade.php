@extends('traffic-control::layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Traffic Control Dashboard</h1>
        <a href="{{ route('traffic-control.dashboard') }}" class="btn btn-primary">
            <i class="bi bi-gear-fill"></i> Control Panel
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Recent Traffic Logs</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">IP Address</th>
                            <th scope="col">Path</th>
                            <th scope="col">Reason</th>
                            <th scope="col">Logged At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->ip }}</td>
                                <td>{{ $log->path }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $log->reason }}</span>
                                </td>
                                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No traffic logs found 🚫
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
