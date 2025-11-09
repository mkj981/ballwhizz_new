<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Stations;

class TvStationsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;

    public $name, $url, $image_path, $type, $related_id, $status;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'name' => 'required|string|max:255',
        'url' => 'nullable|string|max:255',
        'image_path' => 'nullable|string|max:500',
        'type' => 'nullable|string|max:100',
        'related_id' => 'nullable|integer',
        'status' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $station = Stations::findOrFail($id);
        $station->status = !$station->status;
        $station->save();

        session()->flash('success', 'TV Station status updated successfully!');
    }

    public function edit($id)
    {
        $record = Stations::findOrFail($id);
        $this->editingId = $record->id;
        $this->fill($record->toArray());
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'name', 'url', 'image_path', 'type', 'related_id', 'status']);
    }

    public function update()
    {
        $this->validate();

        Stations::where('id', $this->editingId)->update([
            'name' => $this->name,
            'url' => $this->url,
            'image_path' => $this->image_path,
            'type' => $this->type,
            'related_id' => $this->related_id,
            'status' => $this->status ?? true,
        ]);

        $this->cancelEdit();
        session()->flash('success', 'TV Station updated successfully!');
    }

    public function render()
    {
        $records = Stations::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('type', 'like', "%{$this->search}%")
                    ->orWhere('url', 'like', "%{$this->search}%");
            })
            ->orderBy('id', 'ASC')
            ->paginate(10);

        return view('livewire.admin.tv-stations-table', compact('records'));
    }
}
