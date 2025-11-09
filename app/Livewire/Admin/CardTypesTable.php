<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\CardType;
use Illuminate\Support\Facades\File;

class CardTypesTable extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null;
    public $en_name, $ar_name, $multiplier, $image;

    protected $rules = [
        'en_name' => 'required|string|max:255',
        'ar_name' => 'required|string|max:255',
        'multiplier' => 'required|numeric|min:0|max:100',
        'image' => 'nullable|image|max:2048',
    ];

    public function updatingSearch() { $this->resetPage(); }

    public function edit($id)
    {
        $record = CardType::findOrFail($id);
        $this->editingId = $record->id;
        $this->en_name = $record->en_name;
        $this->ar_name = $record->ar_name;
        $this->multiplier = $record->multiplier;
        $this->image = $record->image;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'en_name', 'ar_name', 'multiplier', 'image']);
    }

    public function update()
    {
        $this->validate();
        $record = CardType::findOrFail($this->editingId);

        if ($this->image && !is_string($this->image)) {
            if ($record->image && File::exists(public_path('storage/' . $record->image))) {
                File::delete(public_path('storage/' . $record->image));
            }
            $record->image = $this->image->store('card-types', 'public');
        }

        $record->update([
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
            'multiplier' => $this->multiplier,
            'image' => $record->image,
        ]);

        $this->cancelEdit();
        session()->flash('success', 'Card Type updated successfully!');
    }

    public function render()
    {
        $records = CardType::when($this->search, fn($q) =>
        $q->where('en_name', 'like', "%{$this->search}%")
            ->orWhere('ar_name', 'like', "%{$this->search}%")
        )
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.admin.card-types-table', compact('records'));
    }
}
