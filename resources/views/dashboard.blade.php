<div class="container">
    <h1>Traffic Control Dashboard</h1>
    <table class="table">
        <thead>
            <tr>
                <th>IP</th>
                <th>Path</th>
                <th>Reason</th>
                <th>When</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log->ip }}</td>
                    <td>{{ $log->path }}</td>
                    <td>{{ $log->reason }}</td>
                    <td>{{ $log->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
