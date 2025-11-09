<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Venues;
use App\Models\Countries;

class VenuesTable extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;

    public $country_id, $city_id, $name, $address, $zipcode, $latitude, $longitude, $capacity, $image_path, $city_name, $surface, $national_team, $status;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'country_id' => 'nullable|integer',
        'city_id' => 'nullable|integer',
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'zipcode' => 'nullable|string|max:255',
        'latitude' => 'nullable|string|max:255',
        'longitude' => 'nullable|string|max:255',
        'capacity' => 'nullable|integer',
        'image_path' => 'nullable|string|max:500',
        'city_name' => 'nullable|string|max:255',
        'surface' => 'nullable|string|max:255',
        'national_team' => 'boolean',
        'status' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ✅ Toggle Status
    public function toggleStatus($id)
    {
        $venue = Venues::findOrFail($id);
        $venue->status = !$venue->status;
        $venue->save();

        session()->flash('success', 'Venue status updated successfully!');
    }

    // ✅ Edit
    public function edit($id)
    {
        $record = Venues::findOrFail($id);
        $this->editingId = $record->id;
        $this->fill($record->toArray());
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'country_id', 'city_id', 'name', 'address', 'zipcode', 'latitude', 'longitude', 'capacity', 'image_path', 'city_name', 'surface', 'national_team', 'status']);
    }

    // ✅ Update
    public function update()
    {
        $this->validate();

        Venues::where('id', $this->editingId)->update([
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'name' => $this->name,
            'address' => $this->address,
            'zipcode' => $this->zipcode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'capacity' => $this->capacity,
            'image_path' => $this->image_path,
            'city_name' => $this->city_name,
            'surface' => $this->surface,
            'national_team' => $this->national_team ?? false,
            'status' => $this->status ?? true,
        ]);

        $this->cancelEdit();
        session()->flash('success', 'Venue updated successfully!');
    }

    public function render()
    {
        $records = Venues::with('country')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('city_name', 'like', "%{$this->search}%")
                        ->orWhere('surface', 'like', "%{$this->search}%")
                        ->orWhereHas('country', function ($countryQuery) {
                            $countryQuery->where('en_name', 'like', "%{$this->search}%")
                                ->orWhere('ar_name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->orderBy('id', 'ASC')
            ->paginate(10);

        $countries = Countries::pluck('en_name', 'id');

        return view('livewire.admin.venues-table', compact('records', 'countries'));
    }
}
