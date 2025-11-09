<div>
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h3 class="card-title mb-0">🏆 Season Teams Management</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3 g-3 align-items-end">
                <div class="col-md-6">
                    <label for="league_id" class="form-label fw-bold">Select League</label>
                    <select id="league_id" wire:model.live="league_id" class="form-select">
                        <option value="">-- Choose League --</option>
                        @foreach ($leagues as $league)
                            <option value="{{ $league->id }}">{{ $league->id }} - {{ $league->en_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="season_id" class="form-label fw-bold">Select Season</label>
                    <select id="season_id" wire:model.live="season_id" class="form-select" @disabled(!$league_id)>
                        <option value="">-- Choose Season --</option>
                        @foreach ($seasons as $season)
                            <option value="{{ $season->id }}">{{ $season->id }} - {{ $season->name }}</option>
                        @endforeach
                    </select>
                    @if($league_id && $seasons->isEmpty())
                        <small class="text-muted">No seasons found for this league.</small>
                    @endif
                </div>
            </div>

            @if ($season_id)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                        <tr><th>#</th><th>Logo</th><th>Team</th><th>Country</th></tr>
                        </thead>
                        <tbody>
                        @forelse ($teams as $team)
                            <tr>
                                <td>{{ $team->id }}</td>
                                <td><img src="{{ $team->image_path ?? 'https://via.placeholder.com/40x40?text=⚽' }}" width="40" height="40" class="rounded-circle"></td>
                                <td>{{ $team->en_name }}</td>
                                <td>{{ $team->country?->en_name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No teams assigned to this season yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
