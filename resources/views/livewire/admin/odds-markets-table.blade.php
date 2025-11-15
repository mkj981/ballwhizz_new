<div wire:loading.class="opacity-50" wire:target="update,save,delete,edit">
    <div class="card shadow-sm border-0">

        {{-- 🧭 Header --}}
        <div class="card-header bg-dark text-white py-3">
            <div class="d-flex align-items-center">

                <h3 class="card-title mb-0 me-5">
                    🎯 <span class="fw-bold">Odds Markets</span>
                </h3>

                <div class="search-box position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input
                        wire:model.live="search"
                        type="text"
                        class="form-control form-control-sm ps-4"
                        placeholder="Search markets...">
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
                        <th style="width:70px;">#</th>
                        <th style="width:120px;">Legacy ID</th>
                        <th>Name</th>
                        <th>Developer Name</th>
                        <th style="width:170px;" class="text-center">Winning Calc?</th>
                        <th style="width:200px;" class="text-center">Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse ($records as $market)
                        <tr>
                            <td>{{ $market->id }}</td>

                            {{-- Editing row --}}
                            @if ($editingId === $market->id)

                                <td>
                                    <input wire:model="legacy_id" type="number" class="form-control form-control-sm">
                                </td>

                                <td>
                                    <input wire:model="name" type="text" class="form-control form-control-sm">
                                </td>

                                <td>
                                    <input wire:model="developer_name" type="text" class="form-control form-control-sm">
                                </td>

                                <td class="text-center">
                                    <input
                                        type="checkbox"
                                        wire:model="has_winning_calculations"
                                        class="form-check-input"
                                    >
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">

                                        <button wire:click="save" class="btn btn-success btn-sm">
                                            💾 Save
                                        </button>

                                        <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">
                                            ✖ Cancel
                                        </button>

                                    </div>
                                </td>

                            @else
                                {{-- Normal row --}}
                                <td>{{ $market->legacy_id }}</td>
                                <td>{{ $market->name }}</td>
                                <td>{{ $market->developer_name }}</td>

                                <td class="text-center">
                                    @if($market->has_winning_calculations)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">

                                        <button wire:click="edit({{ $market->id }})"
                                                class="btn btn-primary btn-sm">
                                            ✏️ Edit
                                        </button>

                                        <button wire:click="delete({{ $market->id }})"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this market?')">
                                            🗑 Delete
                                        </button>

                                    </div>
                                </td>
                            @endif
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                No markets found.
                            </td>
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
