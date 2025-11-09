<div wire:loading.class="opacity-50" wire:target="update">
    <div class="card shadow-sm border-0">
        {{-- 🧭 Header --}}
        <div class="card-header bg-dark text-white py-3 d-flex align-items-center">
            <h3 class="card-title mb-0 me-5">⚽ Teams Management</h3>
            <div class="search-box position-relative ms-auto">
                <i class="fas fa-search search-icon"></i>
                <input
                    wire:model.live="search"
                    type="text"
                    class="form-control form-control-sm ps-4"
                    placeholder="Search teams...">
            </div>
        </div>

        {{-- 📋 Body --}}
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>EN Name</th>
                        <th>Logo</th>
                        <th>Country</th>
                        <th>Venue</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>

                            @if ($editingId === $record->id)
                                <td><input wire:model="en_name" class="form-control form-control-sm"></td>
                                <td><input wire:model="ar_name" class="form-control form-control-sm"></td>
                                <td>
                                    <select wire:model="country_id" class="form-select form-select-sm">
                                        <option value="">-- Select Country --</option>
                                        @foreach ($countries as $c)
                                            <option value="{{ $c->id }}">{{ $c->en_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select wire:model="venue_id" class="form-select form-select-sm">
                                        <option value="">-- Select Venue --</option>
                                        @foreach ($venues as $v)
                                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input wire:model="type" class="form-control form-control-sm"></td>
                                <td>
                                    <select wire:model="status" class="form-select form-select-sm">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </td>
                                <td class="text-nowrap">
                                    <button wire:click="update" class="btn btn-success btn-sm me-1">💾 Save</button>
                                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">✖ Cancel</button>
                                </td>
                            @else
                                <td>{{ $record->en_name }}</td>
                                <td class="d-flex align-items-center">
                                    @if ($record->image_path)
                                        <img src="{{ $record->image_path }}" alt="{{ $record->en_name }}" class="rounded-circle me-2" width="36" height="36">
                                    @else
                                        <img src="https://via.placeholder.com/36x36?text=⚽" class="rounded-circle me-2" alt="no image">
                                    @endif

                                </td>
                                <td>{{ $record->country?->en_name ?? '—' }}</td>
                                <td>{{ $record->venue?->name ?? '—' }}</td>
                                <td>{{ $record->type ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $record->status ? 'success' : 'secondary' }}">
                                        {{ $record->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="edit({{ $record->id }})" class="btn btn-primary btn-sm">✏️ Edit</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">No teams found.</td>
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
