<div wire:loading.class="opacity-50" wire:target="update,delete,store">
    <div class="card shadow-sm border-0">

        {{-- 🔹 Header --}}
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h3 class="card-title mb-0 me-4">📅 Cards Weeks</h3>
                <div class="search-box position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input wire:model.debounce.500ms="search" type="text"
                           class="form-control form-control-sm ps-4"
                           placeholder="Search by league or matchday...">
                </div>
            </div>

            <button wire:click="$toggle('showCreateForm')" class="btn btn-success btn-sm">
                {{ $showCreateForm ? '− Cancel' : '➕ Add New Week' }}
            </button>
        </div>

        <div class="card-body">

            {{-- ✅ Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ➕ Add Form --}}
            @if ($showCreateForm)
                <div class="card mb-3 border-success shadow-sm">
                    <div class="card-header bg-success text-white py-2">
                        <strong>➕ Add New Week</strong>
                    </div>

                    <div class="card-body">
                        <form wire:submit.prevent="store" class="row g-2 align-items-center">

                            {{-- League --}}
                            <div class="col-md-2">
                                <label class="form-label mb-0 small fw-bold">League</label>
                                <select wire:model="league_id" class="form-select form-select-sm">
                                    <option value="">Select League</option>
                                    @foreach ($leagues as $league)
                                        <option value="{{ $league->id }}">{{ $league->en_name }}</option>
                                    @endforeach
                                </select>
                                @error('league_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Month --}}
                            <div class="col-md-2">
                                <label class="form-label mb-0 small fw-bold">Ballwhizz Week</label>
                                <select wire:model="week_months_id" class="form-select form-select-sm">
                                    <option value="">Select Month</option>
                                    @foreach ($weekMonths as $month)
                                        <option value="{{ $month->id }}">
                                            {{ $month->week_name ?? ('Week ' . $month->week) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('week_months_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Matchday --}}
                            <div class="col-md-1">
                                <label class="form-label mb-0 small fw-bold">Matchday</label>
                                <input wire:model="matchday" type="number" class="form-control form-control-sm">
                                @error('matchday') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Start --}}
                            <div class="col-md-2">
                                <label class="form-label mb-0 small fw-bold">Start</label>
                                <input wire:model="start" type="datetime-local" class="form-control form-control-sm">
                                @error('start') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- End --}}
                            <div class="col-md-2">
                                <label class="form-label mb-0 small fw-bold">End</label>
                                <input wire:model="end" type="datetime-local" class="form-control form-control-sm">
                                @error('end') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Close At --}}
                            <div class="col-md-2">
                                <label class="form-label mb-0 small fw-bold">Close At</label>
                                <input wire:model="close_at" type="datetime-local" class="form-control form-control-sm">
                                @error('close_at') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Checkboxes --}}
                            <div class="col-md-1 text-center">
                                <div class="form-check form-check-inline mt-3">
                                    <input wire:model="is_active" type="checkbox" class="form-check-input" id="activeCheck">
                                    <label for="activeCheck" class="form-check-label small">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input wire:model="is_open" type="checkbox" class="form-check-input" id="openCheck">
                                    <label for="openCheck" class="form-check-label small">Open</label>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="col-md-1 text-end mt-3">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    💾 Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- 🧾 Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>League</th>
                        <th>Ballwhizz Week</th>
                        <th>Matchday</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Close</th>
                        <th>Active</th>
                        <th>Open</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>

                            {{-- Edit Mode --}}
                            @if ($editingId === $record->id)
                                <td>
                                    <select wire:model="league_id" class="form-select form-select-sm">
                                        @foreach ($leagues as $league)
                                            <option value="{{ $league->id }}">{{ $league->en_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select wire:model="week_months_id" class="form-select form-select-sm">
                                        @foreach ($weekMonths as $month)
                                            <option value="{{ $month->id }}">
                                                {{ $month->week_name ?? ('Week ' . $month->week) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input wire:model="matchday" type="number" class="form-control form-control-sm"></td>
                                <td><input wire:model="start" type="datetime-local" class="form-control form-control-sm"></td>
                                <td><input wire:model="end" type="datetime-local" class="form-control form-control-sm"></td>
                                <td><input wire:model="close_at" type="datetime-local" class="form-control form-control-sm"></td>
                                <td><input wire:model="is_active" type="checkbox" class="form-check-input"></td>
                                <td><input wire:model="is_open" type="checkbox" class="form-check-input"></td>
                                <td>
                                    <button wire:click="update" class="btn btn-success btn-sm me-1">💾</button>
                                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">✖</button>
                                </td>

                                {{-- View Mode --}}
                            @else
                                <td>{{ $record->league->en_name ?? '-' }}</td>
                                <td>{{ $record->weekMonth->week_name ?? ('Week ' . ($record->weekMonth->week ?? '-')) }}</td>
                                <td>{{ $record->matchday ?? '-' }}</td>
                                <td>{{ $record->start ? \Carbon\Carbon::parse($record->start)->format('d M Y, H:i') : '-' }}</td>
                                <td>{{ $record->end ? \Carbon\Carbon::parse($record->end)->format('d M Y, H:i') : '-' }}</td>
                                <td>{{ $record->close_at ? \Carbon\Carbon::parse($record->close_at)->format('d M Y, H:i') : '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $record->is_active ? 'success' : 'secondary' }}">
                                        {{ $record->is_active ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $record->is_open ? 'info' : 'dark' }}">
                                        {{ $record->is_open ? 'Open' : 'Closed' }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="edit({{ $record->id }})" class="btn btn-primary btn-sm me-1">✏️</button>
                                    <button wire:click="delete({{ $record->id }})"
                                            onclick="confirm('Are you sure you want to delete this week?') || event.stopImmediatePropagation()"
                                            class="btn btn-danger btn-sm">🗑</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-3">No weeks found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $records->links() }}
            </div>

        </div>
    </div>
</div>
