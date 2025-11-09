<div wire:loading.class="opacity-50" wire:target="update">
    <div class="card shadow-sm border-0">
        {{-- 🧭 Header --}}
        <div class="card-header bg-dark text-white py-3">
            <div class="d-flex align-items-center">
                <h3 class="card-title mb-0 me-5">
                    🔍 <span class="fw-bold">Search</span>
                </h3>

                <div class="search-box position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input
                        wire:model.live="search"
                        type="text"
                        class="form-control form-control-sm ps-4"
                        placeholder="Search API types...">
                </div>
            </div>
        </div>

        {{-- 🧱 Card Body --}}
        <div class="card-body">
            {{-- ✅ Success Message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- 📋 Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>EN Name</th>
                        <th>AR Name</th>
                        <th>Developer Name</th>
                        <th>Model Type</th>
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
                                <td><input wire:model="developer_name" class="form-control form-control-sm"></td>
                                <td><input wire:model="model_type" class="form-control form-control-sm"></td>
                                <td class="text-nowrap">
                                    <button wire:click="update" class="btn btn-success btn-sm me-1">💾 Save</button>
                                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">✖ Cancel</button>
                                </td>
                            @else
                                <td>{{ $record->en_name }}</td>
                                <td>{{ $record->ar_name }}</td>
                                <td>{{ $record->developer_name }}</td>
                                <td>{{ $record->model_type }}</td>
                                <td>
                                    <button wire:click="edit({{ $record->id }})" class="btn btn-primary btn-sm">✏️ Edit</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No API types found.</td>
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
