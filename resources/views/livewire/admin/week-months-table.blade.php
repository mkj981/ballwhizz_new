<div wire:loading.class="opacity-50" wire:target="update,create,delete">
    <div class="card shadow-sm border-0">
        {{-- 🔹 Header --}}
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h3 class="card-title mb-0 me-4">📅 Week Management</h3>
                <div class="search-box position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input wire:model.live="search" type="text"
                           class="form-control form-control-sm ps-4"
                           placeholder="Search week name or number...">
                </div>
            </div>
            <button wire:click="$toggle('showCreateForm')" class="btn btn-success btn-sm">
                {{ $showCreateForm ? '− Cancel' : '➕ Add New Week' }}
            </button>
        </div>

        {{-- ✅ Flash Messages --}}
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- 🆕 Create Form --}}
            @if ($showCreateForm)
                <div class="mb-4 border p-3 rounded bg-light">
                    <h5 class="fw-bold mb-3">➕ Add New Week</h5>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input wire:model="week_name" type="text" class="form-control form-control-sm" placeholder="Week Name">
                            @error('week_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-2">
                            <input wire:model="week" type="number" class="form-control form-control-sm" placeholder="Week #">
                            @error('week') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-3">
                            <input wire:model="start_date" type="datetime-local" class="form-control form-control-sm">
                            @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-3">
                            <input wire:model="end_date" type="datetime-local" class="form-control form-control-sm">
                            @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-1">
                            <button wire:click="create" class="btn btn-success btn-sm w-100">💾 Save</button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 📋 Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Week Name</th>
                        <th>Week #</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>

                            @if ($editingId === $record->id)
                                <td><input wire:model="week_name" class="form-control form-control-sm"></td>
                                <td><input wire:model="week" type="number" class="form-control form-control-sm"></td>
                                <td><input wire:model="start_date" type="datetime-local" class="form-control form-control-sm"></td>
                                <td><input wire:model="end_date" type="datetime-local" class="form-control form-control-sm"></td>
                                <td class="text-nowrap">
                                    <button wire:click="update" class="btn btn-success btn-sm me-1">💾 Save</button>
                                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">✖ Cancel</button>
                                </td>
                            @else
                                <td>{{ $record->week_name }}</td>
                                <td>{{ $record->week }}</td>
                                <td>{{ $record->start_date->format('Y-m-d H:i') }}</td>
                                <td>{{ $record->end_date->format('Y-m-d H:i') }}</td>
                                <td class="text-nowrap">
                                    <button wire:click="edit({{ $record->id }})" class="btn btn-primary btn-sm me-1">✏️ Edit</button>
                                    <button wire:click="delete({{ $record->id }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="btn btn-danger btn-sm">🗑 Delete</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No week records found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 🔢 Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>
