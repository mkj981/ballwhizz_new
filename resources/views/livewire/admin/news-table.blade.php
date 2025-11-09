<div wire:loading.class="opacity-50">
    <div class="card shadow-sm border-0">

        {{-- 🔹 Header --}}
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">📰 News Management</h3>
            <div class="d-flex align-items-center gap-2">
                {{-- 🔍 Search --}}
                <div class="search-box position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input wire:model.debounce.500ms="search" type="text"
                           class="form-control form-control-sm ps-4"
                           placeholder="Search news...">
                </div>

                {{-- ➕ Add New --}}
                <a href="{{ route('admin.news.create') }}" class="btn btn-success btn-sm">➕ Add New</a>

            </div>
        </div>

        {{-- ✅ Success Message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3 shadow-sm">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- 📋 Table --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>EN Title</th>
                        <th>AR Title</th>
                        <th>Image</th>
                        <th>Hashtags</th>
                        <th>Video</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>
                            <td>{{ Str::limit($record->en_title, 50) }}</td>
                            <td>{{ Str::limit($record->ar_title, 50) }}</td>
                            <td>
                                @if ($record->image)
                                    <img src="{{ asset('storage/' . $record->image) }}" class="img-thumbnail" width="70">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $record->hashtags ?? '—' }}</td>
                            <td>{{ $record->video ? '🎥' : '—' }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.news.edit', $record->id) }}" class="btn btn-primary btn-sm me-1">✏️ Edit</a>

                                <button wire:click="delete({{ $record->id }})"
                                        onclick="return confirm('Are you sure you want to delete this news item?')"
                                        class="btn btn-danger btn-sm">🗑️ Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted py-3">No news found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 🔽 Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>

@push('css')
    <style>
        .search-box { position: relative; }
        .search-icon {
            position: absolute;
            top: 50%;
            left: 8px;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 0.9rem;
        }
        .search-box input { padding-left: 24px; }
    </style>
@endpush
