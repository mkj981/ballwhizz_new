<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // ✅ Required for file uploads
use App\Models\Continent;
use Illuminate\Support\Facades\Storage;

class ContinentsTable extends Component
{
    use WithPagination, WithFileUploads; // ✅ Include both traits

    public $search = '';
    public $editingId = null;
    public $code, $en_name, $ar_name, $dark_img, $light_img, $status;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'code' => 'required|string|max:5',
        'en_name' => 'required|string|max:255',
        'ar_name' => 'nullable|string|max:255',
        'dark_img' => 'nullable', // Allow file uploads or strings
        'light_img' => 'nullable',
        'status' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $record = Continent::findOrFail($id);

        $this->editingId = $record->id;
        $this->code = $record->code;
        $this->en_name = $record->en_name;
        $this->ar_name = $record->ar_name;
        $this->dark_img = $record->dark_img;
        $this->light_img = $record->light_img;
        $this->status = $record->status;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'code', 'en_name', 'ar_name', 'dark_img', 'light_img', 'status']);
    }

    public function update()
    {
        $this->validate();

        $record = Continent::findOrFail($this->editingId);

        // ✅ Handle new dark image upload
        if ($this->dark_img && !is_string($this->dark_img)) {
            if ($record->dark_img && Storage::disk('public')->exists($record->dark_img)) {
                Storage::disk('public')->delete($record->dark_img);
            }
            $this->dark_img = $this->dark_img->store('continents', 'public');
        }

        // ✅ Handle new light image upload
        if ($this->light_img && !is_string($this->light_img)) {
            if ($record->light_img && Storage::disk('public')->exists($record->light_img)) {
                Storage::disk('public')->delete($record->light_img);
            }
            $this->light_img = $this->light_img->store('continents', 'public');
        }

        // 🔹 Convert string "1"/"0" to boolean
        $this->status = filter_var($this->status, FILTER_VALIDATE_BOOLEAN);

        $record->update([
            'code' => $this->code,
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
            'dark_img' => $this->dark_img,
            'light_img' => $this->light_img,
            'status' => $this->status,
        ]);

        $this->reset(['editingId', 'code', 'en_name', 'ar_name', 'dark_img', 'light_img', 'status']);
        session()->flash('success', 'Continent updated successfully!');
    }

    public function toggleStatus($id)
    {
        $continent = Continent::findOrFail($id);
        $continent->status = !$continent->status;
        $continent->save();

        session()->flash('success', 'Status updated successfully!');
    }

    public function render()
    {
        $records = Continent::query()
            ->when($this->search, fn($q) =>
            $q->where('en_name', 'like', "%{$this->search}%")
                ->orWhere('ar_name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%")
            )
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.admin.continents-table', compact('records'));
    }
}
