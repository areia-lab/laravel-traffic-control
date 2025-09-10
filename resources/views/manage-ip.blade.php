@extends('traffic-control::layouts.app')

@section('title', 'Manage IPs')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Manage IPs</strong>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Add, block or whitelist IPs easily.</p>

            <div class="row">
                {{-- Blocked IPs --}}
                <div class="col-md-6">
                    <div class="card border-danger mb-4">
                        <div class="card-header bg-danger text-white">
                            <strong>Blocked IPs</strong>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="#">
                                @csrf
                                <div class="input-group mb-3">
                                    <input type="text" name="ip" class="form-control"
                                        placeholder="Enter IP to block">
                                    <button class="btn btn-danger" type="submit">Block</button>
                                </div>
                            </form>

                            @if (!empty($blockedIps))
                                <ul class="list-group">
                                    @foreach ($blockedIps as $ip)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $ip }}</span>
                                            <form method="POST" action="#">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No blocked IPs</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Allowed IPs --}}
                <div class="col-md-6">
                    <div class="card border-success mb-4">
                        <div class="card-header bg-success text-white">
                            <strong>Allowed IPs</strong>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="#">
                                @csrf
                                <div class="input-group mb-3">
                                    <input type="text" name="ip" class="form-control"
                                        placeholder="Enter IP to allow">
                                    <button class="btn btn-success" type="submit">Allow</button>
                                </div>
                            </form>

                            @if (!empty($allowedIps))
                                <ul class="list-group">
                                    @foreach ($allowedIps as $ip)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $ip }}</span>
                                            <form method="POST" action="#">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No allowed IPs</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
