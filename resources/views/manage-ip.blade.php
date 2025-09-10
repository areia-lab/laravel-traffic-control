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
                        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                            <strong>Blocked IPs</strong>
                            <div class="d-flex align-items-center gap-2 w-50">
                                <input type="text" id="blockedSearch" class="form-control form-control-sm"
                                    placeholder="Search...">
                                <span class="badge bg-light text-dark" id="blockedCount">0</span>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Form to add blocked IP --}}
                            <form method="POST" action="{{ route('traffic-control.block-ip') }}">
                                @csrf
                                <div class="input-group mb-3">
                                    <input type="text" name="ip" class="form-control"
                                        placeholder="Enter IP to block">
                                    <button class="btn btn-danger" type="submit">Block</button>
                                </div>
                            </form>

                            @if (!empty($blockedIps))
                                <ul class="list-group" id="blockedList">
                                    @foreach ($blockedIps as $ip)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="ip-text">{{ $ip }}</span>
                                            {{-- Remove blocked IP --}}
                                            <form method="POST"
                                                action="{{ route('traffic-control.remove-block-ip', ['ip' => $ip]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                                <p class="text-muted mt-2 d-none" id="blockedNoResults">No results found</p>
                            @else
                                <p class="text-muted">No blocked IPs</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Allowed IPs --}}
                <div class="col-md-6">
                    <div class="card border-success mb-4">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <strong>Allowed IPs</strong>
                            <div class="d-flex align-items-center gap-2 w-50">
                                <input type="text" id="allowedSearch" class="form-control form-control-sm"
                                    placeholder="Search...">
                                <span class="badge bg-light text-dark" id="allowedCount">0</span>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Form to add allowed IP --}}
                            <form method="POST" action="{{ route('traffic-control.allow-ip') }}">
                                @csrf
                                <div class="input-group mb-3">
                                    <input type="text" name="ip" class="form-control"
                                        placeholder="Enter IP to allow">
                                    <button class="btn btn-success" type="submit">Allow</button>
                                </div>
                            </form>

                            @if (!empty($allowedIps))
                                <ul class="list-group" id="allowedList">
                                    @foreach ($allowedIps as $ip)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="ip-text">{{ $ip }}</span>
                                            {{-- Remove allowed IP --}}
                                            <form method="POST"
                                                action="{{ route('traffic-control.remove-allow-ip', ['ip' => $ip]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                                <p class="text-muted mt-2 d-none" id="allowedNoResults">No results found</p>
                            @else
                                <p class="text-muted">No allowed IPs</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search filter with highlight + badge + sort + no results + reset on ESC --}}
    <script>
        function filterList(inputId, listId, noResultsId, countId) {
            const input = document.getElementById(inputId);
            const list = document.getElementById(listId);
            const noResults = document.getElementById(noResultsId);
            const countBadge = document.getElementById(countId);

            if (!input || !list) return;

            // Store original index for each item
            const items = Array.from(list.getElementsByTagName("li"));
            items.forEach((item, index) => {
                item.dataset.originalIndex = index;
            });

            function runFilter() {
                const filter = input.value.toLowerCase().trim();
                const items = Array.from(list.getElementsByTagName("li"));
                let matchCount = 0;
                let matches = [];
                let nonMatches = [];

                items.forEach(item => {
                    const ipTextEl = item.querySelector(".ip-text");
                    const ipValue = ipTextEl.dataset.original || ipTextEl.innerText;
                    ipTextEl.dataset.original = ipValue;

                    if (filter === "") {
                        item.style.display = "";
                        ipTextEl.innerHTML = ipValue;
                        matchCount++;
                        return;
                    }

                    if (ipValue.toLowerCase().includes(filter)) {
                        item.style.display = "";
                        const regex = new RegExp(`(${filter})`, "gi");
                        ipTextEl.innerHTML = ipValue.replace(regex, `<span class="bg-warning">$1</span>`);
                        matchCount++;
                        matches.push(item);
                    } else {
                        item.style.display = "none";
                        ipTextEl.innerHTML = ipValue;
                        nonMatches.push(item);
                    }
                });

                // Sorting
                if (filter !== "") {
                    // matches first, non-matches hidden after
                    list.innerHTML = "";
                    matches.forEach(m => list.appendChild(m));
                    nonMatches.forEach(m => list.appendChild(m));
                } else {
                    // restore original order
                    list.innerHTML = "";
                    items.sort((a, b) => a.dataset.originalIndex - b.dataset.originalIndex)
                        .forEach(item => list.appendChild(item));
                }

                // Toggle "No results" message
                if (noResults) {
                    noResults.classList.toggle("d-none", matchCount > 0);
                }

                // Update badge count
                if (countBadge) {
                    countBadge.innerText = matchCount;
                }
            }

            input.addEventListener("keyup", function(e) {
                if (e.key === "Escape") input.value = "";
                runFilter();
            });

            runFilter();
        }

        // Init search filters
        filterList("blockedSearch", "blockedList", "blockedNoResults", "blockedCount");
        filterList("allowedSearch", "allowedList", "allowedNoResults", "allowedCount");
    </script>
@endsection
