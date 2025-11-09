<div>
    <div class="mb-3">
        <input wire:model.live="search" type="text" class="form-control" placeholder="Search users...">
    </div>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Social Type</th>
            <th>UID</th>
            <th>Coins</th>
            <th>Gems</th>
            <th>XP</th>
            <th>Joined</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name ?? '-' }}</td>
                <td>{{ $user->social_type ?? '-' }}</td>
                <td>{{ $user->uid ?? '-' }}</td>
                <td>{{ $user->coins }}</td>
                <td>{{ $user->gem }}</td>
                <td>{{ $user->xp }}</td>
                <td>{{ $user->created_at?->format('Y-m-d') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>
