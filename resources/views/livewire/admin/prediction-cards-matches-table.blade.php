<div wire:loading.class="opacity-50" wire:target="update,delete,viewScorers">
    <div class="card shadow-sm border-0">

        {{-- 🔹 Header --}}
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h3 class="card-title mb-0 me-4">⚽ Prediction Matches</h3>
                <div class="search-box position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input wire:model.debounce.500ms="search" type="text" class="form-control form-control-sm ps-4"
                           placeholder="Search by team name...">
                </div>
            </div>
        </div>

        {{-- 🔹 Body --}}
        <div class="card-body">

            {{-- ✅ Success message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- 🧾 Matches Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>League</th>
                        <th>Home</th>
                        <th>Away</th>
                        <th>🕒 Start Time</th>
                        <th>Result</th>
                        <th>Status</th>
                        <th>Prediction Calc</th>
                        <th>Card Calc</th>
                        <th>Scorers</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>

                            {{-- ✏️ Editing Mode --}}
                            @if ($editingId === $record->id)
                                <td>
                                    <select wire:model="league_id" class="form-select form-select-sm">
                                        @foreach ($leagues as $league)
                                            <option value="{{ $league->id }}">{{ $league->en_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select wire:model="home_team_id" class="form-select form-select-sm">
                                        @foreach ($teams as $team)
                                            <option value="{{ $team->id }}">{{ $team->en_name ?? $team->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select wire:model="away_team_id" class="form-select form-select-sm">
                                        @foreach ($teams as $team)
                                            <option value="{{ $team->id }}">{{ $team->en_name ?? $team->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input wire:model="starting_at" type="datetime-local" class="form-control form-control-sm"></td>
                                <td>
                                    <input wire:model="home_team_result" type="number" class="form-control form-control-sm d-inline-block w-25 text-center">
                                    <span class="mx-1">-</span>
                                    <input wire:model="away_team_result" type="number" class="form-control form-control-sm d-inline-block w-25 text-center">
                                </td>
                                <td>
                                    <select wire:model="status" class="form-select form-select-sm">
                                        <option value="0">Pending</option>
                                        <option value="1">Finished</option>
                                    </select>
                                </td>
                                <td>
                                    <select wire:model="prediction_calculate" class="form-select form-select-sm">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </td>
                                <td>
                                    <select wire:model="card_calculate" class="form-select form-select-sm">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </td>
                                <td colspan="2" class="text-center">
                                    <button wire:click="update" class="btn btn-success btn-sm me-2">💾 Save</button>
                                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">✖ Cancel</button>
                                </td>
                            @else
                                {{-- 📄 Normal View --}}
                                <td>{{ $record->league->en_name ?? '-' }}</td>
                                <td>{{ $record->homeTeam->en_name ?? $record->homeTeam->name ?? '-' }}</td>
                                <td>{{ $record->awayTeam->en_name ?? $record->awayTeam->name ?? '-' }}</td>
                                <td>{{ $record->starting_at ? \Carbon\Carbon::parse($record->starting_at)->format('d M Y, H:i') : '-' }}</td>
                                <td>{{ $record->home_team_result ?? '-' }} - {{ $record->away_team_result ?? '-' }}</td>
                                <td><span class="badge bg-{{ $record->status ? 'success' : 'warning' }}">{{ $record->status ? 'Finished' : 'Pending' }}</span></td>
                                <td><span class="badge bg-{{ $record->prediction_calculate ? 'success' : 'secondary' }}">{{ $record->prediction_calculate ? 'Yes' : 'No' }}</span></td>
                                <td><span class="badge bg-{{ $record->card_calculate ? 'success' : 'secondary' }}">{{ $record->card_calculate ? 'Yes' : 'No' }}</span></td>
                                <td class="text-center">
                                    <button wire:click="viewScorers({{ $record->id }})" class="btn btn-info btn-sm">👟 View</button>
                                </td>
                                <td class="text-center">
                                    <button wire:click="edit({{ $record->id }})" class="btn btn-primary btn-sm me-1">✏️</button>
                                    <button wire:click="delete({{ $record->id }})"
                                            onclick="confirm('Are you sure you want to delete this match?') || event.stopImmediatePropagation()"
                                            class="btn btn-danger btn-sm">🗑</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-center text-muted py-3">No matches found.</td></tr>
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

    {{-- ⚽ Scorers Modal --}}
    @if ($showScorers && $selectedMatch)
        <div class="modal fade show d-block" style="background:rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">
                            ⚽ Scorers – {{ $selectedMatch->homeTeam->en_name ?? '-' }} vs {{ $selectedMatch->awayTeam->en_name ?? '-' }}
                        </h5>
                        <button wire:click="closeScorers" class="btn-close btn-close-white"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-sm table-striped align-middle">
                            <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Player</th>
                                <th>Team Side</th>
                                <th>Minute</th>
                                <th>Type</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($selectedMatch->scorers as $scorer)
                                <tr>
                                    <td>{{ $scorer->id }}</td>
                                    <td>{{ $scorer->player->en_common_name ?? $scorer->player->en_name ?? $scorer->player->name ?? 'Unknown' }}</td>
                                    <td>{{ ucfirst($scorer->team_side) }}</td>
                                    <td>{{ $scorer->minute ?? '-' }}</td>
                                    <td>{{ ucfirst($scorer->type ?? '-') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No scorers yet.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button wire:click="closeScorers" class="btn btn-secondary">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
