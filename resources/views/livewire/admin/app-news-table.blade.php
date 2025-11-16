<div wire:loading.class="opacity-50">

    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">📰 App News</h3>

            <div class="d-flex gap-2">
                <input wire:model.live="search"
                       type="text"
                       class="form-control form-control-sm"
                       placeholder="Search news...">

                <button wire:click="create" class="btn btn-success btn-sm">
                    ➕ Add New
                </button>
            </div>
        </div>

        <div class="card-body">

            {{-- SUCCESS MESSAGE --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- CREATE/EDIT FORM --}}
            @if ($showForm)
                <div class="border rounded p-3 mb-4 bg-light">

                    <h5 class="mb-3">{{ $editingId ? '✏️ Edit News' : '🆕 Create News' }}</h5>

                    {{-- Short Texts --}}
                    <div class="row mb-2">
                        <div class="col">
                            <input wire:model="short_text_en"
                                   class="form-control"
                                   placeholder="Short Text EN">
                        </div>
                        <div class="col">
                            <input wire:model="short_text_ar"
                                   class="form-control"
                                   placeholder="Short Text AR">
                        </div>
                    </div>

                    {{-- Long Texts --}}
                    <textarea wire:model="long_text_en" class="form-control mb-2" rows="3"
                              placeholder="Long Text EN"></textarea>

                    <textarea wire:model="long_text_ar" class="form-control mb-2" rows="3"
                              placeholder="Long Text AR"></textarea>

                    {{-- Video --}}
                    <input wire:model="video_url" class="form-control mb-2" placeholder="Video URL">

                    {{-- Existing Images --}}
                    @if (!empty($images))
                        <label class="fw-bold mt-3">Existing Images</label>

                        <div class="d-flex flex-wrap gap-2 mt-2">

                            @foreach ($images as $index => $img)
                                <div class="position-relative">

                                    <img src="{{ asset('storage/' . $img) }}"
                                         style="width:70px; height:70px; object-fit:cover; border-radius:6px; border:1px solid #ddd;">

                                    {{-- Remove image --}}
                                    <button type="button"
                                            wire:click="removeImage({{ $index }})"
                                            class="btn btn-sm btn-danger"
                                            style="
                                                position:absolute;
                                                top:-6px;
                                                right:-6px;
                                                padding:2px 6px;
                                                border-radius:50%;
                                                font-size:10px;
                                            ">
                                        ✖
                                    </button>

                                </div>
                            @endforeach

                        </div>
                    @endif

                    {{-- Upload New Images --}}
                    <label class="fw-bold mt-3">Upload Images</label>
                    <input type="file" wire:model="uploadedImages" multiple class="form-control mb-3">

                    {{-- Buttons --}}
                    <button wire:click="save" class="btn btn-success">💾 Save</button>
                    <button wire:click="$set('showForm', false)" class="btn btn-secondary">✖ Cancel</button>

                </div>
            @endif

            {{-- TABLE --}}
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Short EN</th>
                    <th>Short AR</th>
                    <th>Images</th>
                    <th width="150">Actions</th>
                </tr>
                </thead>

                <tbody>
                @foreach ($records as $item)
                    <tr>
                        <td>{{ $item->id }}</td>

                        <td>{{ Str::limit($item->short_text_en, 40) }}</td>
                        <td>{{ Str::limit($item->short_text_ar, 40) }}</td>

                        <td class="text-center">

                            @if ($item->images && count($item->images) > 0)

                                <div class="d-flex justify-content-center gap-1 flex-wrap">

                                    @foreach (array_slice($item->images, 0, 3) as $img)
                                        <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $img) }}"
                                                 style="width:45px; height:45px; object-fit:cover; border-radius:4px;">
                                        </a>
                                    @endforeach

                                    @if (count($item->images) > 3)
                                        <span class="badge bg-dark ms-1">
                                            +{{ count($item->images) - 3 }}
                                        </span>
                                    @endif

                                </div>

                            @else
                                <span class="badge bg-secondary">None</span>
                            @endif

                        </td>

                        <td class="text-center">
                            <button class="btn btn-sm btn-primary me-1"
                                    wire:click="edit({{ $item->id }})">
                                ✏️
                            </button>

                            <button class="btn btn-sm btn-danger"
                                    wire:click="delete({{ $item->id }})"
                                    onclick="return confirm('Delete this news?')">
                                🗑
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $records->links() }}
            </div>

        </div>
    </div>

</div>
