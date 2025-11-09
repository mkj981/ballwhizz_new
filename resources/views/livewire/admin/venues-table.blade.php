<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Venues Management</h3>
            <input type="text" wire:model.live="search" class="form-control w-25" placeholder="Search venues...">
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-center align-middle">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Country</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Capacity</th>
                    <th>Surface</th>
                    <th>National</th>
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
                                <img src="{{ $record->image_path }}" alt="Venue" style="width:45px; height:45px; border-radius:6px; object-fit:cover;">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $record->country?->en_name }}</td>

                        @if ($editingId === $record->id)
                            <td><input type="text" wire:model="name" class="form-control"></td>
                            <td><input type="text" wire:model="city_name" class="form-control"></td>
                            <td><input type="number" wire:model="capacity" class="form-control"></td>
                            <td><input type="text" wire:model="surface" class="form-control"></td>
                            <td>
                                <select wire:model="national_team" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </td>
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
                            <td>{{ $record->city_name }}</td>
                            <td>{{ $record->capacity ?? '—' }}</td>
                            <td>{{ ucfirst($record->surface ?? '—') }}</td>
                            <td>
                                    <span class="badge {{ $record->national_team ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ $record->national_team ? 'Yes' : 'No' }}
                                    </span>
                            </td>
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
