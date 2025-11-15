<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OddsBookmaker;

class OddsBookmakersTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $name, $legacy_id, $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'legacy_id' => 'nullable|integer',
    ];

    public function render()
    {
        $records = OddsBookmaker::when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('legacy_id', 'like', "%{$this->search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.odds-bookmakers-table', compact('records'));
    }

    public function edit($id)
    {
        $record = OddsBookmaker::findOrFail($id);

        $this->editingId = $record->id;
        $this->legacy_id = $record->legacy_id;
        $this->name = $record->name;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'legacy_id', 'name']);
    }

    public function save()
    {
        $this->validate();

        // Prevent negative legacy IDs
        $legacy = $this->legacy_id < 0 ? null : $this->legacy_id;

        OddsBookmaker::updateOrCreate(
            ['id' => $this->editingId],
            [
                'legacy_id' => $legacy,
                'name'      => $this->name,
            ]
        );

        $this->reset(['editingId', 'legacy_id', 'name']);

        session()->flash('success', 'Bookmaker saved successfully.');
    }

    public function delete($id)
    {
        OddsBookmaker::findOrFail($id)->delete();
        session()->flash('success', 'Bookmaker deleted successfully.');
    }
}
