<div wire:loading.class="opacity-50" wire:target="update,save,delete,edit">
    <div class="card shadow-sm border-0">

        {{-- 🧭 Header --}}
        <div class="card-header bg-dark text-white py-3">
            <div class="d-flex align-items-center">

                <h3 class="card-title mb-0 me-5">
                    📊 <span class="fw-bold">Odds Bookmakers</span>
                </h3>

                <div class="search-box position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input
                        wire:model.live="search"
                        type="text"
                        class="form-control form-control-sm ps-4"
                        placeholder="Search bookmakers...">
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
                        <th style="width: 70px;">#</th>
                        <th style="width: 130px;">Legacy ID</th>
                        <th>Name</th>
                        <th class="text-center" style="width: 200px;">Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse ($records as $bk)
                        <tr>
                            <td>{{ $bk->id }}</td>

                            {{-- Editing Row --}}
                            @if ($editingId === $bk->id)

                                <td>
                                    <input wire:model="legacy_id" type="number" class="form-control form-control-sm">
                                </td>

                                <td>
                                    <input wire:model="name" type="text" class="form-control form-control-sm">
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
                                {{-- Normal Display Row --}}
                                <td>{{ $bk->legacy_id }}</td>
                                <td>{{ $bk->name }}</td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">

                                        <button wire:click="edit({{ $bk->id }})"
                                                class="btn btn-primary btn-sm">
                                            ✏️ Edit
                                        </button>


                                    </div>
                                </td>
                            @endif
                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                No bookmakers found.
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
