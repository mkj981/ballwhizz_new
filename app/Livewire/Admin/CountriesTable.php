<?php

namespace App\Livewire\Admin;

use App\Models\Continent;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Countries;

class CountriesTable extends Component
{
    use WithPagination;

    // ✅ Use Bootstrap pagination layout (AdminLTE friendly)
    protected string $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null;
    public $en_name, $ar_name, $continent_id, $fifa_name, $iso2, $iso3, $latitude, $longitude, $borders, $image_path, $status;

    protected $rules = [
        'en_name' => 'required|string|max:255',
        'ar_name' => 'required|string|max:255',
        'continent_id' => 'required|exists:continents,id',
        'fifa_name' => 'nullable|string|max:50',
        'iso2' => 'nullable|string|max:2',
        'iso3' => 'nullable|string|max:3',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'borders' => 'nullable|string',
        'image_path' => 'nullable|string',
        'status' => 'boolean',
    ];

    // ✅ Reset pagination when user types in the search bar
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $records = Countries::with('continent')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('en_name', 'like', "%{$this->search}%")
                        ->orWhere('ar_name', 'like', "%{$this->search}%")
                        ->orWhere('fifa_name', 'like', "%{$this->search}%")
                        ->orWhere('iso2', 'like', "%{$this->search}%")
                        ->orWhere('iso3', 'like', "%{$this->search}%");
                })
                    // ✅ Search by related continent name too
                    ->orWhereHas('continent', function ($q) {
                        $q->where('en_name', 'like', "%{$this->search}%")
                            ->orWhere('ar_name', 'like', "%{$this->search}%");
                    });
            })
            ->orderBy('en_name')
            ->paginate(10);

        return view('livewire.admin.countries-table', [
            'records' => $records,
            'continents' => Continent::orderBy('en_name')->get(),
        ]);
    }


    public function edit($id)
    {
        $country = Countries::findOrFail($id);
        $this->editingId = $id;
        $this->fill($country->toArray());
    }

    public function cancelEdit()
    {
        $this->reset([
            'editingId',
            'en_name',
            'ar_name',
            'continent_id',
            'fifa_name',
            'iso2',
            'iso3',
            'latitude',
            'longitude',
            'borders',
            'image_path',
            'status',
        ]);
    }

    public function update()
    {
        $this->validate();

        Countries::findOrFail($this->editingId)->update([
            'en_name'      => $this->en_name,
            'ar_name'      => $this->ar_name,
            'continent_id' => $this->continent_id,
            'fifa_name'    => $this->fifa_name,
            'iso2'         => $this->iso2,
            'iso3'         => $this->iso3,
            'latitude'     => $this->latitude,
            'longitude'    => $this->longitude,
            'borders'      => $this->borders,
            'image_path'   => $this->image_path,
            'status'       => $this->status,
        ]);

        session()->flash('success', 'Country updated successfully ✅');
        $this->cancelEdit();
    }

    public function toggleStatus($id)
    {
        $country = Countries::findOrFail($id);
        $country->update(['status' => !$country->status]);
    }
}
