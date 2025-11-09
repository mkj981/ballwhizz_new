<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\File;
use App\Models\News;

class NewsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $record = News::find($id);
        if ($record) {
            if ($record->image && file_exists(public_path('storage/' . $record->image))) {
                File::delete(public_path('storage/' . $record->image));
            }
            $record->players()->detach();
            $record->teams()->detach();
            $record->leagues()->detach();
            $record->delete();
            session()->flash('success', '🗑️ News deleted successfully!');
        }
    }

    public function render()
    {
        $records = News::query()
            ->when($this->search, fn($q) =>
            $q->where('en_title', 'like', "%{$this->search}%")
                ->orWhere('ar_title', 'like', "%{$this->search}%")
                ->orWhere('hashtags', 'like', "%{$this->search}%"))
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.news-table', compact('records'))
            ->extends('adminlte::page')
            ->section('content');
    }

}
