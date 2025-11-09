<div wire:loading.class="opacity-50" wire:target="create,update,delete">
    <div class="card shadow-sm border-0">

        {{-- Header --}}
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h3 class="card-title mb-0 me-4">🏷 Positions</h3>
                <div class="search-box position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input wire:model.debounce.500ms="search" type="text" class="form-control form-control-sm ps-4"
                           placeholder="Search by code or name...">
                </div>
            </div>

            <button wire:click="$toggle('showCreateForm')" class="btn btn-success btn-sm">
                {{ $showCreateForm ? '− Cancel' : '➕ Add New Position' }}
            </button>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Create Form --}}
            @if ($showCreateForm)
                <div class="card mb-3 border-success shadow-sm">
                    <div class="card-header bg-success text-white py-2">
                        <strong>➕ Add New Position</strong>
                    </div>
                    <div class="card-body row g-3 align-items-center">
                        <div class="col-md-2">
                            <input wire:model="code" type="text" class="form-control form-control-sm" placeholder="Code">
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <input wire:model="en_name" type="text" class="form-control form-control-sm" placeholder="English Name">
                            @error('en_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4">
                            <input wire:model="ar_name" type="text" class="form-control form-control-sm" placeholder="Arabic Name">
                            @error('ar_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-2 text-end">
                            <button wire:click="create" class="btn btn-success btn-sm">💾 Save</button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>English Name</th>
                        <th>Arabic Name</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($records as $record)
                        <tr>
                            @if ($editingId === $record->id)
                                <td>{{ $record->id }}</td>
                                <td><input wire:model="code" type="text" class="form-control form-control-sm"></td>
                                <td><input wire:model="en_name" type="text" class="form-control form-control-sm"></td>
                                <td><input wire:model="ar_name" type="text" class="form-control form-control-sm"></td>
                                <td>
                                    <button wire:click="update" class="btn btn-success btn-sm me-1">💾</button>
                                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">✖</button>
                                </td>
                            @else
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->code }}</td>
                                <td>{{ $record->en_name }}</td>
                                <td>{{ $record->ar_name }}</td>
                                <td>
                                    <button wire:click="edit({{ $record->id }})" class="btn btn-primary btn-sm me-1">✏️</button>
                                    <button wire:click="delete({{ $record->id }})"
                                            onclick="confirm('Delete this position?') || event.stopImmediatePropagation()"
                                            class="btn btn-danger btn-sm">🗑</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No positions found.</td></tr>
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
