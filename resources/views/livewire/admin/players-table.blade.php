<div>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">⚽ Players Management</h3>
            <input type="text" wire:model.live="search" class="form-control w-25" placeholder="Search player...">
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-center align-middle mb-0">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>English Name</th>
                    <th>Arabic Name</th>

                    <th>League</th>
                    <th>Team</th>

                    <th>DOB</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>
                @forelse ($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>

                        {{-- 🖼 Player Image --}}
                        <td>
                            @if ($record->image_path && file_exists(public_path('storage/' . $record->image_path)))
                                <img src="{{ asset('storage/' . $record->image_path) }}" width="40" height="40"
                                     class="rounded-circle" style="object-fit: cover;">
                            @else
                                <img src="https://via.placeholder.com/40x40?text=⚽" width="40" height="40" class="rounded-circle">
                            @endif

                            {{-- Upload new image (edit mode) --}}
                            @if ($editingId === $record->id)
                                <input type="file" wire:model="image" class="form-control mt-2">
                                @if ($image)
                                    <div class="mt-1">
                                        <small class="text-muted">Preview:</small><br>
                                        <img src="{{ $image->temporaryUrl() }}" width="50" height="50" class="rounded mt-1" style="object-fit: cover;">
                                    </div>
                                @endif
                            @endif
                        </td>

                        {{-- ✏️ Edit mode --}}
                        @if ($editingId === $record->id)
                            <td><input type="text" wire:model="en_common_name" class="form-control" placeholder="English name"></td>
                            <td><input type="text" wire:model="ar_common_name" class="form-control" placeholder="Arabic name"></td>

                            <td colspan="5">
                                <button wire:click="update" class="btn btn-success btn-sm">💾 Save</button>
                                <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">❌ Cancel</button>
                            </td>
                        @else
                            {{-- Normal display --}}
                            <td>{{ $record->en_common_name ?? '—' }}</td>
                            <td>{{ $record->ar_common_name ?? '—' }}</td>

                            <td>{{ $record->league?->en_name ?? '—' }}</td>
                            <td>{{ $record->team?->en_name ?? '—' }}</td>

                            <td>{{ $record->date_of_birth ?? '—' }}</td>

                            <td>
                                <button wire:click="edit({{ $record->id }})" class="btn btn-warning btn-sm">
                                    ✏️ Edit
                                </button>
                                <button wire:click="delete({{ $record->id }})"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this player?')">
                                    🗑️ Delete
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="py-3 text-muted">No players found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $records->links() }}
        </div>
    </div>

    {{-- ✅ Flash message --}}
    @if (session()->has('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
</div>
