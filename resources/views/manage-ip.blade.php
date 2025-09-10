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
                            <input type="text" id="blockedSearch" class="form-control form-control-sm w-50"
                                placeholder="Search...">
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
                                <ul class="list-group" id="blockedList">
                                    @foreach ($blockedIps as $ip)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="ip-text">{{ $ip }}</span>
                                            <form method="POST" action="#">
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
                            <input type="text" id="allowedSearch" class="form-control form-control-sm w-50"
                                placeholder="Search...">
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
                                <ul class="list-group" id="allowedList">
                                    @foreach ($allowedIps as $ip)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="ip-text">{{ $ip }}</span>
                                            <form method="POST" action="#">
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

    {{-- Search filter with highlight + no results + reset on ESC --}}
    <script>
        function filterList(inputId, listId, noResultsId) {
            const input = document.getElementById(inputId);
            const list = document.getElementById(listId);
            const noResults = document.getElementById(noResultsId);

            if (!input || !list) return;

            function runFilter() {
                const filter = input.value.toLowerCase().trim();
                const items = list.getElementsByTagName("li");

                let matchCount = 0;

                for (let i = 0; i < items.length; i++) {
                    const ipTextEl = items[i].querySelector(".ip-text");
                    const ipValue = ipTextEl.dataset.original || ipTextEl.innerText;

                    // Store original text for reset
                    ipTextEl.dataset.original = ipValue;

                    if (filter === "") {
                        items[i].style.display = "";
                        ipTextEl.innerHTML = ipValue;
                        matchCount++;
                        continue;
                    }

                    if (ipValue.toLowerCase().includes(filter)) {
                        items[i].style.display = "";
                        const regex = new RegExp(`(${filter})`, "gi");
                        ipTextEl.innerHTML = ipValue.replace(regex, `<span class="bg-warning">$1</span>`);
                        matchCount++;
                    } else {
                        items[i].style.display = "none";
                        ipTextEl.innerHTML = ipValue;
                    }
                }

                // Toggle "No results" message
                if (noResults) {
                    noResults.classList.toggle("d-none", matchCount > 0);
                }
            }

            // Filter while typing
            input.addEventListener("keyup", function(e) {
                if (e.key === "Escape") {
                    // Reset on ESC
                    input.value = "";
                }
                runFilter();
            });

            // Initial reset
            runFilter();
        }

        // Init search filters
        filterList("blockedSearch", "blockedList", "blockedNoResults");
        filterList("allowedSearch", "allowedList", "allowedNoResults");
    </script>
@endsection
