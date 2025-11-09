<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Teams;
use App\Models\Countries;
use App\Models\Venues;

class TeamsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;

    public $en_name, $ar_name, $country_id, $venue_id, $type, $status;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'en_name' => 'required|string|max:255',
        'ar_name' => 'nullable|string|max:255',
        'country_id' => 'nullable|integer|exists:countries,id',
        'venue_id' => 'nullable|integer|exists:venues,id',
        'type' => 'nullable|string|max:255',
        'status' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $record = Teams::findOrFail($id);
        $this->editingId = $record->id;
        $this->en_name = $record->en_name;
        $this->ar_name = $record->ar_name;
        $this->country_id = $record->country_id;
        $this->venue_id = $record->venue_id;
        $this->type = $record->type;
        $this->status = $record->status;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'en_name', 'ar_name', 'country_id', 'venue_id', 'type', 'status']);
    }

    public function update()
    {
        $this->validate();

        Teams::where('id', $this->editingId)->update([
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
            'country_id' => $this->country_id,
            'venue_id' => $this->venue_id,
            'type' => $this->type,
            'status' => $this->status ?? true,
        ]);

        $this->reset(['editingId', 'en_name', 'ar_name', 'country_id', 'venue_id', 'type', 'status']);
        session()->flash('success', '✅ Team updated successfully!');
    }

    public function render()
    {
        $records = Teams::query()
            ->with(['country', 'venue'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('en_name', 'like', "%{$this->search}%")
                        ->orWhere('ar_name', 'like', "%{$this->search}%")
                        ->orWhere('type', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('id', 'ASC') // ✅ Always ordered by latest ID
            ->paginate(10);

        $countries = Countries::orderBy('en_name')->get(['id', 'en_name']);
        $venues = Venues::orderBy('name')->get(['id', 'name']);

        return view('livewire.admin.teams-table', compact('records', 'countries', 'venues'));
    }

}
