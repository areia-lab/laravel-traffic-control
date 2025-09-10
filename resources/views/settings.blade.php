@extends('traffic-control::layouts.app')

@section('title', 'Settings')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-gear-fill me-2"></i> Traffic Control Settings</h5>
                    </div>

                    <div class="card-body">
                        <!-- Global Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('traffic-settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Tabs -->
                            <ul class="nav nav-tabs mb-4" role="tablist">
                                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab"
                                        data-bs-target="#general" type="button">General</button></li>
                                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#rate"
                                        type="button">Rate Limits</button></li>
                                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ip"
                                        type="button">IP Control</button></li>
                                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#bot"
                                        type="button">Bot Detection</button></li>
                                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#alerts"
                                        type="button">Alerts</button></li>
                                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#dashboard" type="button">Dashboard</button></li>
                                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#api"
                                        type="button">API Quota</button></li>
                                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#logging"
                                        type="button">Logging</button></li>
                            </ul>

                            <div class="tab-content">

                                <!-- General -->
                                <div class="tab-pane fade show active" id="general">
                                    <div class="mb-3">
                                        <label class="form-label">Enabled</label>
                                        <select name="enabled" class="form-select @error('enabled') is-invalid @enderror">
                                            @php
                                                $enabled = old(
                                                    'enabled',
                                                    env('TRAFFIC_CONTROL_ENABLED', $config['enabled']),
                                                );
                                            @endphp
                                            <option value="1" {{ $enabled ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ !$enabled ? 'selected' : '' }}>No</option>
                                        </select>
                                        @error('enabled')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Storage</label>
                                        <select name="storage" class="form-select @error('storage') is-invalid @enderror">
                                            @php
                                                $storage = old(
                                                    'storage',
                                                    env('TRAFFIC_CONTROL_STORAGE', $config['storage']),
                                                );
                                            @endphp
                                            <option value="redis" {{ $storage === 'redis' ? 'selected' : '' }}>Redis
                                            </option>
                                            <option value="database" {{ $storage === 'database' ? 'selected' : '' }}>
                                                Database</option>
                                            <option value="file" {{ $storage === 'file' ? 'selected' : '' }}>File</option>
                                        </select>
                                        @error('storage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Rate Limits -->
                                <div class="tab-pane fade" id="rate">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Default</h6>
                                            <div class="mb-2">
                                                <input type="number"
                                                    class="form-control @error('rate_limits.default.requests') is-invalid @enderror"
                                                    name="rate_limits[default][requests]"
                                                    value="{{ old('rate_limits.default.requests', $config['rate_limits']['default']['requests']) }}">
                                                @error('rate_limits.default.requests')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-2">
                                                <input type="number"
                                                    class="form-control @error('rate_limits.default.per') is-invalid @enderror"
                                                    name="rate_limits[default][per]"
                                                    value="{{ old('rate_limits.default.per', $config['rate_limits']['default']['per']) }}">
                                                @error('rate_limits.default.per')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>API</h6>
                                            <div class="mb-2">
                                                <input type="number"
                                                    class="form-control @error('rate_limits.api.requests') is-invalid @enderror"
                                                    name="rate_limits[api][requests]"
                                                    value="{{ old('rate_limits.api.requests', $config['rate_limits']['api']['requests']) }}">
                                                @error('rate_limits.api.requests')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-2">
                                                <input type="number"
                                                    class="form-control @error('rate_limits.api.per') is-invalid @enderror"
                                                    name="rate_limits[api][per]"
                                                    value="{{ old('rate_limits.api.per', $config['rate_limits']['api']['per']) }}">
                                                @error('rate_limits.api.per')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- IP Control -->
                                <div class="tab-pane fade" id="ip">
                                    <div class="mb-3">
                                        <label class="form-label">Blacklist</label>
                                        <textarea name="ip[blacklist]" rows="2" class="form-control @error('ip.blacklist') is-invalid @enderror">{{ old('ip.blacklist', implode(',', $config['ip']['blacklist'])) }}</textarea>
                                        @error('ip.blacklist')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Whitelist</label>
                                        <textarea name="ip[whitelist]" rows="2" class="form-control @error('ip.whitelist') is-invalid @enderror">{{ old('ip.whitelist', implode(',', $config['ip']['whitelist'])) }}</textarea>
                                        @error('ip.whitelist')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Block TOR</label>
                                        <select name="ip[block_tor]"
                                            class="form-select @error('ip.block_tor') is-invalid @enderror">
                                            <option value="1"
                                                {{ old('ip.block_tor', $config['ip']['block_tor']) ? 'selected' : '' }}>Yes
                                            </option>
                                            <option value="0"
                                                {{ !old('ip.block_tor', $config['ip']['block_tor']) ? 'selected' : '' }}>No
                                            </option>
                                        </select>
                                        @error('ip.block_tor')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Bot Detection -->
                                <div class="tab-pane fade" id="bot">
                                    <div class="mb-3">
                                        <label class="form-label">Enable Bot Detection</label>
                                        <select name="bot_detection[enabled]"
                                            class="form-select @error('bot_detection.enabled') is-invalid @enderror">
                                            <option value="1"
                                                {{ old('bot_detection.enabled', $config['bot_detection']['enabled']) ? 'selected' : '' }}>
                                                Yes</option>
                                            <option value="0"
                                                {{ !old('bot_detection.enabled', $config['bot_detection']['enabled']) ? 'selected' : '' }}>
                                                No</option>
                                        </select>
                                        @error('bot_detection.enabled')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">User Agents</label>
                                        <textarea name="bot_detection[user_agents]" rows="2"
                                            class="form-control @error('bot_detection.user_agents') is-invalid @enderror">{{ old('bot_detection.user_agents', implode(',', $config['bot_detection']['user_agents'])) }}</textarea>
                                        @error('bot_detection.user_agents')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Alerts -->
                                <div class="tab-pane fade" id="alerts">
                                    <div class="mb-3">
                                        <label class="form-label">Slack Webhook</label>
                                        <input type="text"
                                            class="form-control @error('alerts.slack') is-invalid @enderror"
                                            name="alerts[slack]"
                                            value="{{ old('alerts.slack', $config['alerts']['slack']) }}">
                                        @error('alerts.slack')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email"
                                            class="form-control @error('alerts.email') is-invalid @enderror"
                                            name="alerts[email]"
                                            value="{{ old('alerts.email', $config['alerts']['email']) }}">
                                        @error('alerts.email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Threshold</label>
                                        <input type="number"
                                            class="form-control @error('alerts.threshold') is-invalid @enderror"
                                            name="alerts[threshold]"
                                            value="{{ old('alerts.threshold', $config['alerts']['threshold']) }}">
                                        @error('alerts.threshold')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Dashboard -->
                                <div class="tab-pane fade" id="dashboard">
                                    <div class="mb-3">
                                        <label class="form-label">Enabled</label>
                                        <select name="dashboard[enabled]"
                                            class="form-select @error('dashboard.enabled') is-invalid @enderror">
                                            <option value="1"
                                                {{ old('dashboard.enabled', $config['dashboard']['enabled']) ? 'selected' : '' }}>
                                                Yes</option>
                                            <option value="0"
                                                {{ !old('dashboard.enabled', $config['dashboard']['enabled']) ? 'selected' : '' }}>
                                                No</option>
                                        </select>
                                        @error('dashboard.enabled')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Prefix</label>
                                        <input type="text"
                                            class="form-control @error('dashboard.prefix') is-invalid @enderror"
                                            name="dashboard[prefix]"
                                            value="{{ old('dashboard.prefix', $config['dashboard']['prefix']) }}">
                                        @error('dashboard.prefix')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Middleware</label>
                                        <input type="text"
                                            class="form-control @error('dashboard.middleware') is-invalid @enderror"
                                            name="dashboard[middleware][]"
                                            value="{{ old('dashboard.middleware', implode(',', $config['dashboard']['middleware'])) }}">
                                        @error('dashboard.middleware')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- API Quota -->
                                <div class="tab-pane fade" id="api">
                                    <div class="mb-3">
                                        <label class="form-label">Default API Quota</label>
                                        <input type="number"
                                            class="form-control @error('api_quota.default') is-invalid @enderror"
                                            name="api_quota[default]"
                                            value="{{ old('api_quota.default', $config['api_quota']['default']) }}">
                                        @error('api_quota.default')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Logging -->
                                <div class="tab-pane fade" id="logging">
                                    <div class="mb-3">
                                        <label class="form-label">Log Blocked Requests</label>
                                        <select name="logging[log_blocked]"
                                            class="form-select @error('logging.log_blocked') is-invalid @enderror">
                                            <option value="1"
                                                {{ old('logging.log_blocked', $config['logging']['log_blocked']) ? 'selected' : '' }}>
                                                Yes</option>
                                            <option value="0"
                                                {{ !old('logging.log_blocked', $config['logging']['log_blocked']) ? 'selected' : '' }}>
                                                No</option>
                                        </select>
                                        @error('logging.log_blocked')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Sample Rate</label>
                                        <input type="number"
                                            class="form-control @error('logging.log_sample_rate') is-invalid @enderror"
                                            name="logging[log_sample_rate]"
                                            value="{{ old('logging.log_sample_rate', $config['logging']['log_sample_rate']) }}">
                                        @error('logging.log_sample_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bi bi-check-circle me-1"></i> Save Changes
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
