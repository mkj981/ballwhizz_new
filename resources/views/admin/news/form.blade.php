<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3">
            {{-- Titles --}}
            <div class="col-md-6">
                <label>EN Title</label>
                <input type="text" name="en_title" value="{{ old('en_title', $news->en_title ?? '') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
                <label>AR Title</label>
                <input type="text" name="ar_title" value="{{ old('ar_title', $news->ar_title ?? '') }}" class="form-control form-control-sm">
            </div>

            {{-- Descriptions --}}
            <div class="col-md-6">
                <label>EN Short Description</label>
                <input type="text" name="en_short_desc" value="{{ old('en_short_desc', $news->en_short_desc ?? '') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
                <label>AR Short Description</label>
                <input type="text" name="ar_short_desc" value="{{ old('ar_short_desc', $news->ar_short_desc ?? '') }}" class="form-control form-control-sm">
            </div>

            {{-- Text --}}
            <div class="col-md-6">
                <label>EN Text</label>
                <textarea id="en_editor" name="en_text" class="form-control form-control-sm">{{ old('en_text', $news->en_text ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
                <label>AR Text</label>
                <textarea id="ar_editor" name="ar_text" class="form-control form-control-sm">{{ old('ar_text', $news->ar_text ?? '') }}</textarea>
            </div>

            {{-- Hashtags + Video --}}
            <div class="col-md-6">
                <label>Hashtags</label>
                <input type="text" name="hashtags" value="{{ old('hashtags', $news->hashtags ?? '') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
                <label>Video URL</label>
                <input type="text" name="video" value="{{ old('video', $news->video ?? '') }}" class="form-control form-control-sm">
            </div>

            {{-- Image --}}
            <div class="col-md-6">
                <label>Image</label>
                <input type="file" name="image" class="form-control form-control-sm">
                @if (!empty($news->image))
                    <img src="{{ asset('storage/' . $news->image) }}" class="img-thumbnail mt-2" width="100">
                @endif
            </div>

            {{-- Relations --}}
            <div class="col-md-4">
                <label>Players</label>
                <select name="players[]" multiple class="form-control select2">
                    @foreach($players as $p)
                        <option value="{{ $p->id }}" @selected(in_array($p->id, $selectedPlayers ?? []))>{{ $p->en_common_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Teams</label>
                <select name="teams[]" multiple class="form-control select2">
                    @foreach($teams as $t)
                        <option value="{{ $t->id }}" @selected(in_array($t->id, $selectedTeams ?? []))>{{ $t->en_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Leagues</label>
                <select name="leagues[]" multiple class="form-control select2">
                    @foreach($leagues as $l)
                        <option value="{{ $l->id }}" @selected(in_array($l->id, $selectedLeagues ?? []))>{{ $l->en_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="text-end mt-3">
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">← Back</a>
            <button type="submit" class="btn btn-primary btn-sm">💾 Save</button>
        </div>
    </div>
</div>

@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector('#en_editor'));
        ClassicEditor.create(document.querySelector('#ar_editor'), { language: { ui: 'en', content: 'ar' } });
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
    </script>
@endpush
