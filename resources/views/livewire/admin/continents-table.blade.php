<div wire:loading.class="opacity-50" wire:target="toggleStatus,update">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3">
            <div class="d-flex align-items-center">
                <h3 class="card-title mb-0 me-5  card-title-spaced">
                    🔍 <span class="fw-bold">Search</span>
                </h3>
                <div class="search-box position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input
                        wire:model.live="search"
                        type="text"
                        class="form-control form-control-sm ps-4"
                        placeholder="Search continents...">
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- ✅ Success Message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- 🌍 Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>EN Name</th>
                        <th>AR Name</th>
                        <th>Dark Img</th>
                        <th>Light Img</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>

                            {{-- ✏️ Edit Mode --}}
                            @if ($editingId === $record->id)
                                <td><input wire:model="code" class="form-control form-control-sm"></td>
                                <td><input wire:model="en_name" class="form-control form-control-sm"></td>
                                <td><input wire:model="ar_name" class="form-control form-control-sm"></td>

                                {{-- 🔹 Dark Image Upload --}}
                                <td class="text-center">
                                    <div class="upload-wrapper">
                                        @if ($dark_img)
                                            @if (is_string($dark_img))
                                                <img src="{{ asset('storage/' . $dark_img) }}" class="img-preview mb-1">
                                            @else
                                                <img src="{{ $dark_img->temporaryUrl() }}" class="img-preview mb-1">
                                            @endif
                                        @elseif($record->dark_img)
                                            <img src="{{ asset('storage/' . $record->dark_img) }}" class="img-preview mb-1">
                                        @else
                                            <div class="placeholder-box mb-1">No Image</div>
                                        @endif

                                        <label class="custom-upload-btn">
                                            <i class="fas fa-upload me-1"></i> Upload
                                            <input type="file" wire:model="dark_img" hidden>
                                        </label>

                                        @error('dark_img')
                                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </td>

                                {{-- 🔹 Light Image Upload --}}
                                <td class="text-center">
                                    <div class="upload-wrapper">
                                        @if ($light_img)
                                            @if (is_string($light_img))
                                                <img src="{{ asset('storage/' . $light_img) }}" class="img-preview mb-1">
                                            @else
                                                <img src="{{ $light_img->temporaryUrl() }}" class="img-preview mb-1">
                                            @endif
                                        @elseif($record->light_img)
                                            <img src="{{ asset('storage/' . $record->light_img) }}" class="img-preview mb-1">
                                        @else
                                            <div class="placeholder-box mb-1">No Image</div>
                                        @endif

                                        <label class="custom-upload-btn">
                                            <i class="fas fa-upload me-1"></i> Upload
                                            <input type="file" wire:model="light_img" hidden>
                                        </label>

                                        @error('light_img')
                                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </td>

                                {{-- 🔹 Status Select --}}
                                <td>
                                    <select wire:model="status" class="form-select form-select-sm" style="display: none">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </td>

                                {{-- 🔹 Actions --}}
                                <td class="text-nowrap">
                                    <button wire:click="update" class="btn btn-success btn-sm me-1">💾 Save</button>
                                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">✖ Cancel</button>
                                </td>

                                {{-- 👁️ View Mode --}}
                            @else
                                <td>{{ $record->code }}</td>
                                <td>{{ $record->en_name }}</td>
                                <td>{{ $record->ar_name }}</td>

                                {{-- 🖼️ Dark Image --}}
                                <td class="text-center">
                                    @if ($record->dark_img)
                                        <img src="{{ asset('storage/' . $record->dark_img) }}" class="img-preview shadow-sm">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- 🖼️ Light Image --}}
                                <td class="text-center">
                                    @if ($record->light_img)
                                        <img src="{{ asset('storage/' . $record->light_img) }}" class="img-preview shadow-sm">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- 🔄 Status Toggle --}}
                                <td>
                                    <button
                                        wire:click="toggleStatus({{ $record->id }})"
                                        class="btn btn-sm {{ $record->status ? 'btn-success' : 'btn-danger' }}">
                                        {{ $record->status ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>

                                {{-- ⚙️ Edit --}}
                                <td>
                                    <button wire:click="edit({{ $record->id }})" class="btn btn-primary btn-sm">
                                        ✏️ Edit
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">No continents found.</td>
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
