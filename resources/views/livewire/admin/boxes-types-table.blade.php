<div wire:loading.class="opacity-50">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">🎁 Boxes Types</h3>
            <div class="search-box position-relative">
                <i class="fas fa-search search-icon"></i>
                <input wire:model.live="search" type="text" class="form-control form-control-sm ps-4"
                       placeholder="Search boxes...">
            </div>
        </div>

        <div class="card-body">
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
                        <th>Image</th>
                        <th>EN Name</th>
                        <th>Gold</th>
                        <th>Silver</th>
                        <th>Bronze</th>
                        <th>Special</th>
                        <th>Gem</th>
                        <th>Coins</th>
                        <th>XP</th>
                        <th>Swap</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>

                            {{-- 🖼 Image --}}
                            <td>
                                @if ($record->image)
                                    <img src="{{ asset('storage/'.$record->image) }}" width="60" height="60" class="rounded shadow-sm">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Edit Mode --}}
                            @if ($editingId === $record->id)
                                <td colspan="8">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label>EN Name</label>
                                            <input wire:model="en_name" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Time (hrs)</label>
                                            <input wire:model="time" type="number" class="form-control form-control-sm">
                                        </div>

                                        <div class="col-md-3">
                                            <label>Gold</label>
                                            <input wire:model="gold_players" type="number" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Silver</label>
                                            <input wire:model="silver_players" type="number" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Bronze</label>
                                            <input wire:model="bronze_players" type="number" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Special</label>
                                            <input wire:model="special_players" type="number" class="form-control form-control-sm">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Gem</label>
                                            <input wire:model="gem" type="number" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Coins</label>
                                            <input wire:model="coins" type="number" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-4">
                                            <label>XP</label>
                                            <input wire:model="xp" type="number" class="form-control form-control-sm">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Price</label>
                                            <input wire:model="price" type="number" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Swap Power</label>
                                            <input wire:model="swap_power" type="number" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Gem Cost</label>
                                            <input wire:model="gem_cost" type="number" class="form-control form-control-sm">
                                        </div>

                                        {{-- 🖼 Image Upload --}}
                                        <div class="col-md-6 text-center">
                                            <label>Main Image</label>
                                            <div class="mb-2">
                                                @if ($image)
                                                    @if (is_string($image))
                                                        <img src="{{ asset('storage/'.$image) }}" width="80" class="rounded shadow-sm">
                                                    @else
                                                        <img src="{{ $image->temporaryUrl() }}" width="80" class="rounded shadow-sm">
                                                    @endif
                                                @endif
                                            </div>
                                            <input type="file" wire:model="image" class="form-control form-control-sm">
                                        </div>

                                        {{-- 🖼 Open Image Upload --}}
                                        <div class="col-md-6 text-center">
                                            <label>Open Image</label>
                                            <div class="mb-2">
                                                @if ($open_image)
                                                    @if (is_string($open_image))
                                                        <img src="{{ asset('storage/'.$open_image) }}" width="80" class="rounded shadow-sm">
                                                    @else
                                                        <img src="{{ $open_image->temporaryUrl() }}" width="80" class="rounded shadow-sm">
                                                    @endif
                                                @endif
                                            </div>
                                            <input type="file" wire:model="open_image" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </td>
                                <td colspan="2">
                                    <button wire:click="update" class="btn btn-success btn-sm mb-1">💾 Save</button>
                                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">✖ Cancel</button>
                                </td>

                            @else
                                {{-- View Mode --}}
                                <td>{{ $record->en_name }}</td>
                                <td>{{ $record->gold_players }}</td>
                                <td>{{ $record->silver_players }}</td>
                                <td>{{ $record->bronze_players }}</td>
                                <td>{{ $record->special_players }}</td>
                                <td>{{ $record->gem }}</td>
                                <td>{{ $record->coins }}</td>
                                <td>{{ $record->xp }}</td>

                                {{-- 🔁 Swap Toggle --}}
                                <td>
                                    <button wire:click="toggleSwap({{ $record->id }})"
                                            class="btn btn-sm {{ $record->swap ? 'btn-success' : 'btn-danger' }}">
                                        {{ $record->swap ? 'On' : 'Off' }}
                                    </button>
                                </td>

                                <td>
                                    <button wire:click="edit({{ $record->id }})"
                                            class="btn btn-primary btn-sm">✏️ Edit</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-3">No boxes found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>
