<div wire:loading.class="opacity-50">
    <div class="card shadow-sm border-0">

        {{-- Header --}}
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">🎴 Players Cards</h3>
            <div class="search-box position-relative">
                <i class="fas fa-search search-icon"></i>
                <input wire:model.live="search" type="text" class="form-control form-control-sm ps-4"
                       placeholder="Search player name...">
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body">

            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Player</th>
                        <th>League</th>
                        <th>Team</th>
                        <th>Card Type</th>
                        <th>Energy</th>
                        <th>Week/Month</th>
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
                                    <select wire:model="player_id" class="form-select form-select-sm">
                                        <option value="">-- select player --</option>
                                        @foreach ($players as $p)
                                            <option value="{{ $p->id }}">{{ $p->en_common_name }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td colspan="2" class="text-muted">Auto based on player</td>

                                <td>
                                    <select wire:model="type_id" class="form-select form-select-sm">
                                        <option value="">-- select type --</option>
                                        @foreach ($types as $t)
                                            <option value="{{ $t->id }}">{{ $t->en_name }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td><input wire:model="energy" type="number" class="form-control form-control-sm"></td>
                                <td><input wire:model="week_id" type="number" class="form-control form-control-sm"></td>

                                <td class="text-nowrap">
                                    <button wire:click="update" class="btn btn-success btn-sm me-1">💾 Save</button>
                                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">✖ Cancel</button>
                                </td>

                                {{-- View Mode --}}
                            @else
                                <td>{{ $record->player?->en_common_name ?? '—' }}</td>
                                <td>{{ $record->player?->league?->en_name ?? '—' }}</td>
                                <td>{{ $record->player?->team?->en_name ?? '—' }}</td>
                                <td>{{ $record->type?->en_name ?? '—' }}</td>
                                <td>{{ $record->energy }}</td>
                                <td>{{ $record->week_id ?? '—' }}</td>
                                <td>
                                    <button wire:click="edit({{ $record->id }})"
                                            class="btn btn-primary btn-sm">✏️ Edit</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted py-3">No player cards found.</td>
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
