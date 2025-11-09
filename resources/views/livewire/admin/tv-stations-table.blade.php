<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">TV Stations Management</h3>
            <input type="text" wire:model.live="search" class="form-control w-25" placeholder="Search stations...">
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-center align-middle">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>URL</th>
                    <th>Related ID</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>
                            @if($record->image_path)
                                <img src="{{ $record->image_path }}" style="width:45px; height:45px; border-radius:6px; object-fit:cover;">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Inline Editing --}}
                        @if ($editingId === $record->id)
                            <td><input type="text" wire:model="name" class="form-control"></td>
                            <td><input type="text" wire:model="type" class="form-control"></td>
                            <td><input type="text" wire:model="url" class="form-control"></td>
                            <td><input type="number" wire:model="related_id" class="form-control"></td>
                            <td>
                                <select wire:model="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </td>
                            <td>
                                <button wire:click="update" class="btn btn-success btn-sm">Save</button>
                                <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">Cancel</button>
                            </td>
                        @else
                            <td>{{ $record->name }}</td>
                            <td>{{ ucfirst($record->type ?? '—') }}</td>
                            <td>
                                @if($record->url)
                                    <a href="{{ $record->url }}" target="_blank">{{ Str::limit($record->url, 30) }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $record->related_id ?? '—' }}</td>
                            <td>
                                <button wire:click="toggleStatus({{ $record->id }})"
                                        class="btn btn-sm {{ $record->status ? 'btn-success' : 'btn-danger' }}">
                                    {{ $record->status ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td>
                                <button wire:click="edit({{ $record->id }})" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $records->links() }}
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mt-2">
            {{ session('success') }}
        </div>
    @endif
</div>
