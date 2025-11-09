<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Leagues Management</h3>
            <input type="text" wire:model.live="search" class="form-control w-25" placeholder="Search...">
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-center align-middle">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th> {{-- 🖼 New --}}
                    <th>Country</th>
                    <th>English Name</th>
                    <th>Arabic Name</th>
                    <th>Type</th>
                    <th>Prediction</th>
                    <th>Cards</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>

                        {{-- 🖼 League Image --}}
                        <td>
                            @if($record->image_path)
                                <img src="{{ asset($record->image_path) }}"
                                     alt="League Logo"
                                     style="width:40px; height:40px; border-radius:6px; object-fit:cover;">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>{{ $record->country?->en_name }}</td>

                        {{-- Inline Editing --}}
                        @if ($editingId === $record->id)
                            <td><input type="text" wire:model="en_name" class="form-control"></td>
                            <td><input type="text" wire:model="ar_name" class="form-control"></td>
                            <td><input type="text" wire:model="type" class="form-control"></td>

                            <td colspan="2">
                                <button wire:click="update" class="btn btn-success btn-sm">Save</button>
                                <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">Cancel</button>
                            </td>
                            <td></td>
                        @else
                            <td>{{ $record->en_name }}</td>
                            <td>{{ $record->ar_name }}</td>
                            <td>{{ $record->type }}</td>

                            {{-- Toggle Status --}}
                            <td>
                                <button wire:click="toggleStatus({{ $record->id }})"
                                        class="btn btn-sm {{ $record->status ? 'btn-success' : 'btn-danger' }}">
                                    {{ $record->status ? 'Enabled' : 'Disabled' }}
                                </button>
                            </td>

                            {{-- Toggle Cards Status --}}
                            <td>
                                <button wire:click="toggleCardsStatus({{ $record->id }})"
                                        class="btn btn-sm {{ $record->cards_status ? 'btn-info' : 'btn-secondary' }}">
                                    {{ $record->cards_status ? 'Enabled' : 'Disabled' }}
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
