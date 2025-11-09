<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Positions;

class PositionsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null;
    public $showCreateForm = false;

    public $code, $en_name, $ar_name;

    protected $rules = [
        'code' => 'required|string|max:10|unique:positions,code',
        'en_name' => 'required|string|max:255',
        'ar_name' => 'nullable|string|max:255',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->validate();

        Positions::create([
            'code' => strtoupper($this->code),
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
        ]);

        $this->reset(['code', 'en_name', 'ar_name', 'showCreateForm']);
        session()->flash('success', '✅ Position created successfully!');
    }

    public function edit($id)
    {
        $record = Positions::findOrFail($id);
        $this->editingId = $record->id;
        $this->code = $record->code;
        $this->en_name = $record->en_name;
        $this->ar_name = $record->ar_name;
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|string|max:10|unique:positions,code,' . $this->editingId,
            'en_name' => 'required|string|max:255',
            'ar_name' => 'nullable|string|max:255',
        ]);

        $record = Positions::findOrFail($this->editingId);
        $record->update([
            'code' => strtoupper($this->code),
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
        ]);

        $this->cancelEdit();
        session()->flash('success', '✅ Position updated successfully!');
    }

    public function delete($id)
    {
        $record = Positions::findOrFail($id);
        $record->delete();

        session()->flash('success', '🗑 Position deleted successfully!');
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'code', 'en_name', 'ar_name']);
    }

    public function render()
    {
        $records = Positions::query()
            ->when($this->search, fn($q) =>
            $q->where('en_name', 'like', "%{$this->search}%")
                ->orWhere('ar_name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%")
            )
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.admin.positions-table', compact('records'));
    }
}
