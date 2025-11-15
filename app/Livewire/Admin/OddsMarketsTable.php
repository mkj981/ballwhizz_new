<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OddsMarket;

class OddsMarketsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null;

    public $legacy_id, $name, $developer_name, $has_winning_calculations = 0;

    protected $rules = [
        'legacy_id' => 'nullable|integer',
        'name' => 'required|string|max:255',
        'developer_name' => 'nullable|string|max:255',
        'has_winning_calculations' => 'boolean',
    ];

    public function render()
    {
        $records = OddsMarket::when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('developer_name', 'like', "%{$this->search}%")
                ->orWhere('legacy_id', 'like', "%{$this->search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.odds-markets-table', compact('records'));
    }

    public function edit($id)
    {
        $market = OddsMarket::findOrFail($id);

        $this->editingId = $market->id;
        $this->legacy_id = $market->legacy_id;
        $this->name = $market->name;
        $this->developer_name = $market->developer_name;
        $this->has_winning_calculations = $market->has_winning_calculations;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'legacy_id', 'name', 'developer_name', 'has_winning_calculations']);
    }

    public function save()
    {
        $this->validate();

        OddsMarket::updateOrCreate(
            ['id' => $this->editingId],
            [
                'legacy_id' => $this->legacy_id < 0 ? null : $this->legacy_id,
                'name' => $this->name,
                'developer_name' => $this->developer_name,
                'has_winning_calculations' => $this->has_winning_calculations ? 1 : 0,
            ]
        );

        session()->flash('success', 'Market saved successfully.');

        $this->cancelEdit();
    }

    public function delete($id)
    {
        OddsMarket::findOrFail($id)->delete();
        session()->flash('success', 'Market deleted successfully.');
    }
}
