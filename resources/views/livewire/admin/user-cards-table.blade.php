<div wire:loading.class="opacity-50" wire:target="store,update,delete">
    <div class="card shadow-sm border-0">

        {{-- Header --}}
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">🃏 User Cards</h3>
            <div class="d-flex align-items-center gap-2">
                <input wire:model.debounce.500ms="search" type="text" class="form-control form-control-sm"
                       placeholder="Search by user or player name...">
                <button wire:click="$toggle('showCreateForm')" class="btn btn-success btn-sm">
                    {{ $showCreateForm ? '− Cancel' : '➕ Add New' }}
                </button>
            </div>
        </div>

        <div class="card-body">
            {{-- Alerts --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Add Form --}}
            @if ($showCreateForm)
                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <select wire:model="user_id" class="form-select form-select-sm">
                                <option value="">Select User</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->id }} - {{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select wire:model="card_id" class="form-select form-select-sm">
                                <option value="">Select Card / Player</option>
                                @foreach($cards as $c)
                                    <option value="{{ $c->id }}">
                                        #{{ $c->id }} • {{ $c->player->en_common_name ?? $c->player->en_name ?? 'Unknown' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select wire:model="league_id" class="form-select form-select-sm">
                                <option value="">League</option>
                                @foreach($leagues as $l)
                                    <option value="{{ $l->id }}">{{ $l->en_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select wire:model="position_id" class="form-select form-select-sm">
                                <option value="">Position</option>
                                @foreach($positions as $p)
                                    <option value="{{ $p->id }}">{{ $p->en_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 text-center">
                            <input wire:model="is_in_team" type="checkbox" class="form-check-input">
                            <small>Team</small>
                        </div>
                        <div class="col-md-1 text-center">
                            <input wire:model="is_sub" type="checkbox" class="form-check-input">
                            <small>Sub</small>
                        </div>
                        <div class="col-md-1">
                            <input wire:model="in_stad" type="number" class="form-control form-control-sm" placeholder="#">
                        </div>
                        <div class="col-md-2 text-end">
                            <button wire:click="store" class="btn btn-success btn-sm">💾 Save</button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th wire:click="sortBy('user_cards.id')" style="cursor:pointer">
                            ID {!! $sortField === 'user_cards.id' ? ($sortDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </th>
                        <th wire:click="sortBy('user_name')" style="cursor:pointer">
                            User {!! $sortField === 'user_name' ? ($sortDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </th>
                        <th wire:click="sortBy('card_id')" style="cursor:pointer">
                            Card {!! $sortField === 'card_id' ? ($sortDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </th>
                        <th wire:click="sortBy('player_name')" style="cursor:pointer">
                            Player {!! $sortField === 'player_name' ? ($sortDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </th>
                        <th wire:click="sortBy('league_name')" style="cursor:pointer">
                            League {!! $sortField === 'league_name' ? ($sortDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </th>
                        <th wire:click="sortBy('position_name')" style="cursor:pointer">
                            Position {!! $sortField === 'position_name' ? ($sortDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </th>
                        <th>Team</th>
                        <th>Sub</th>
                        <th wire:click="sortBy('in_stad')" style="cursor:pointer">
                            In Stad {!! $sortField === 'in_stad' ? ($sortDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($records as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->user_name ?? '—' }}</td>
                            <td>#{{ $r->card_id }}</td>
                            <td>{{ $r->player_name ?? '—' }}</td>
                            <td>{{ $r->league_name ?? '—' }}</td>
                            <td>{{ $r->position_name ?? '—' }}</td>
                            <td>{{ $r->is_in_team ? '✅' : '❌' }}</td>
                            <td>{{ $r->is_sub ? '✅' : '❌' }}</td>
                            <td>{{ $r->in_stad }}</td>
                            <td>
                                <button wire:click="edit({{ $r->id }})" class="btn btn-sm btn-primary me-1">✏️</button>
                                <button wire:click="delete({{ $r->id }})"
                                        onclick="confirm('Delete this record?') || event.stopImmediatePropagation()"
                                        class="btn btn-sm btn-danger">🗑</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No records found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-center">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>
