<div wire:loading.class="opacity-50" wire:target="toggleStatus,update">
    <div class="card shadow-sm border-0">

        {{-- 🔍 Search Header --}}
        <div class="card-header bg-dark text-white py-3 d-flex align-items-center flex-wrap">
            <h3 class="card-title mb-0 d-flex align-items-center flex-wrap">
                🔍 <span class="fw-bold me-3">Search</span>

                <div class="search-box position-relative" style="width: 250px;">
                    <i class="fas fa-search search-icon"></i>
                    <input
                        wire:model.live="search"
                        type="text"
                        class="form-control form-control-sm ps-4"
                        placeholder="Search countries...">
                </div>
            </h3>
        </div>

        {{-- 🧾 Card Body --}}
        <div class="card-body pb-0">
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
                        <th>AR Name</th>
                        <th>Continent</th>
                        <th>FIFA</th>
                        <th>ISO2</th>
                        <th>ISO3</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
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
                                    <select wire:model="continent_id" class="form-select form-select-sm">
                                        @foreach ($continents as $continent)
                                            <option value="{{ $continent->id }}">{{ $continent->en_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input wire:model="fifa_name" class="form-control form-control-sm"></td>
                                <td><input wire:model="iso2" class="form-control form-control-sm"></td>
                                <td><input wire:model="iso3" class="form-control form-control-sm"></td>
                                <td><input wire:model="latitude" class="form-control form-control-sm"></td>
                                <td><input wire:model="longitude" class="form-control form-control-sm"></td>
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
                                <td>{{ $record->ar_name }}</td>
                                <td>{{ $record->continent->en_name ?? '—' }}</td>
                                <td>{{ $record->fifa_name ?? '—' }}</td>
                                <td>{{ $record->iso2 ?? '—' }}</td>
                                <td>{{ $record->iso3 ?? '—' }}</td>
                                <td>{{ $record->latitude ?? '—' }}</td>
                                <td>{{ $record->longitude ?? '—' }}</td>
                                <td>
                                    <button wire:click="toggleStatus({{ $record->id }})"
                                            class="btn btn-sm {{ $record->status ? 'btn-success' : 'btn-danger' }}">
                                        {{ $record->status ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td>
                                    <button wire:click="edit({{ $record->id }})" class="btn btn-primary btn-sm">
                                        ✏️ Edit
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-3">No countries found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 🔢 Pagination --}}
        @if ($records->count())
            <div class="card-footer bg-white border-0 py-3 text-center">
                <div class="d-flex justify-content-center">
                    {{ $records->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
