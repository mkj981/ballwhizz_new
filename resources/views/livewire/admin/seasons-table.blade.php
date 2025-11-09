<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Seasons Management</h3>
            <input type="text" wire:model.live="search" class="form-control w-25" placeholder="Search...">
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-center align-middle">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>League</th>
                    <th>Name</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Current</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->league?->en_name }}</td>

                        @if ($editingId === $record->id)
                            <td><input type="text" wire:model="name" class="form-control"></td>
                            <td><input type="date" wire:model="starting_at" class="form-control"></td>
                            <td><input type="date" wire:model="ending_at" class="form-control"></td>

                            <td colspan="2">
                                <button wire:click="update" class="btn btn-success btn-sm">Save</button>
                                <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">Cancel</button>
                            </td>
                            <td></td>
                        @else
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->starting_at }}</td>
                            <td>{{ $record->ending_at }}</td>

                            {{-- Toggle Current --}}
                            <td>
                                <button wire:click="toggleCurrent({{ $record->id }})"
                                        class="btn btn-sm {{ $record->is_current ? 'btn-primary' : 'btn-secondary' }}">
                                    {{ $record->is_current ? 'Yes' : 'No' }}
                                </button>
                            </td>

                            {{-- Toggle Status --}}
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
