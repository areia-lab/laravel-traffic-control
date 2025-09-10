@extends('traffic-control::layouts.app')

@section('title', 'Manage IPs')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Manage IPs</strong>
        </div>
        <div class="card-body">
            <p class="text-muted">Manage IPs Here</p>

            <h5 class="mt-3">Blocked IPs</h5>
            @if (!empty($blockedIps))
                <ul class="list-group mb-3">
                    @foreach ($blockedIps as $ip)
                        <li class="list-group-item">{{ $ip }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-danger">No blocked IPs</p>
            @endif

            <h5 class="mt-3">Allowed IPs</h5>
            @if (!empty($allowedIps))
                <ul class="list-group mb-3">
                    @foreach ($allowedIps as $ip)
                        <li class="list-group-item">{{ $ip }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-success">No allowed IPs</p>
            @endif
        </div>
    </div>
@endsection
