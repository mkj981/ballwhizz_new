<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ApiType;

class ApiTypesTable extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;
    public $en_name, $ar_name, $developer_name, $model_type;

    protected $paginationTheme = 'bootstrap';
    protected $rules = [
        'en_name' => 'required|string|max:255',
        'ar_name' => 'nullable|string|max:255',
        'developer_name' => 'nullable|string|max:255',
        'model_type' => 'nullable|string|max:255',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $record = ApiType::findOrFail($id);
        $this->editingId = $record->id;
        $this->en_name = $record->en_name;
        $this->ar_name = $record->ar_name;
        $this->developer_name = $record->developer_name;
        $this->model_type = $record->model_type;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'en_name', 'ar_name', 'developer_name', 'model_type']);
    }

    public function update()
    {
        $this->validate();

        ApiType::where('id', $this->editingId)->update([
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
            'developer_name' => $this->developer_name,
            'model_type' => $this->model_type,
        ]);

        $this->reset(['editingId', 'en_name', 'ar_name', 'developer_name', 'model_type']);
        session()->flash('success', 'API Type updated successfully!');
    }

    public function render()
    {
        $records = ApiType::query()
            ->when($this->search, fn($q) =>
            $q->where('en_name', 'like', "%{$this->search}%")
                ->orWhere('ar_name', 'like', "%{$this->search}%")
                ->orWhere('developer_name', 'like', "%{$this->search}%")
                ->orWhere('model_type', 'like', "%{$this->search}%")
            )
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.api-types-table', compact('records'));
    }
}
