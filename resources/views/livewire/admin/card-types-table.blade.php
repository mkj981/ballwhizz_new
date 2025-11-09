<div wire:loading.class="opacity-50">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">🃏 Card Types</h3>
            <div class="search-box position-relative">
                <i class="fas fa-search search-icon"></i>
                <input wire:model.live="search" type="text" class="form-control form-control-sm ps-4"
                       placeholder="Search cards...">
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
                        <th>AR Name</th>
                        <th>Multiplier</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>
                            <td>
                                @if ($record->image)
                                    <img src="{{ asset('storage/'.$record->image) }}" width="50" height="50" class="rounded shadow-sm">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            @if ($editingId === $record->id)
                                <td><input wire:model="en_name" class="form-control form-control-sm"></td>
                                <td><input wire:model="ar_name" class="form-control form-control-sm"></td>
                                <td><input wire:model="multiplier" type="number" step="0.01" class="form-control form-control-sm"></td>
                                <td class="text-nowrap">
                                    <div class="mb-2">
                                        @if ($image)
                                            @if (is_string($image))
                                                <img src="{{ asset('storage/'.$image) }}" width="50" class="rounded shadow-sm mb-1">
                                            @else
                                                <img src="{{ $image->temporaryUrl() }}" width="50" class="rounded shadow-sm mb-1">
                                            @endif
                                        @endif
                                    </div>
                                    <input type="file" wire:model="image" class="form-control form-control-sm mb-2">
                                    <button wire:click="update" class="btn btn-success btn-sm me-1">💾 Save</button>
                                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">✖ Cancel</button>
                                </td>
                            @else
                                <td>{{ $record->en_name }}</td>
                                <td>{{ $record->ar_name }}</td>
                                <td>{{ $record->multiplier }}</td>
                                <td>
                                    <button wire:click="edit({{ $record->id }})" class="btn btn-primary btn-sm">✏️ Edit</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No card types found.</td>
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
