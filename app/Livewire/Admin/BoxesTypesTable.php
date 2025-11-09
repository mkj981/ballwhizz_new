<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\BoxesType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
class BoxesTypesTable extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null;

    // Fields
    public $en_name, $en_description, $ar_description, $time,
        $gold_players, $silver_players, $bronze_players, $special_players,
        $gem, $coins, $xp, $price, $swap, $swap_power, $gem_cost,
        $image, $open_image, $en_swap_trade_in_desc, $ar_swap_trade_in_desc,
        $en_swap_buy_desc, $ar_swap_buy_desc;

    protected $rules = [
        'en_name' => 'required|string|max:255',
        'en_description' => 'nullable|string',
        'ar_description' => 'nullable|string',
        'time' => 'nullable|numeric',
        'gold_players' => 'nullable|numeric',
        'silver_players' => 'nullable|numeric',
        'bronze_players' => 'nullable|numeric',
        'special_players' => 'nullable|numeric',
        'gem' => 'nullable|numeric',
        'coins' => 'nullable|numeric',
        'xp' => 'nullable|numeric',
        'price' => 'nullable|numeric',
        'swap' => 'boolean',
        'swap_power' => 'nullable|numeric',
        'gem_cost' => 'nullable|numeric',
        'image' => 'nullable|image|max:2048',
        'open_image' => 'nullable|image|max:2048',
        'en_swap_trade_in_desc' => 'nullable|string',
        'ar_swap_trade_in_desc' => 'nullable|string',
        'en_swap_buy_desc' => 'nullable|string',
        'ar_swap_buy_desc' => 'nullable|string',
    ];

    public function updatingSearch() { $this->resetPage(); }

    public function toggleSwap($id)
    {
        $box = BoxesType::find($id);
        if ($box) {
            $box->swap = !$box->swap;
            $box->save();
        }
    }

    public function edit($id)
    {
        $record = BoxesType::findOrFail($id);
        $this->editingId = $record->id;

        foreach ($this->rules as $key => $rule) {
            if (in_array($key, ['image', 'open_image'])) continue;
            $this->$key = $record->$key;
        }

        $this->image = $record->image;
        $this->open_image = $record->open_image;
    }

    public function cancelEdit()
    {
        $this->reset([
            'editingId', 'en_name', 'en_description', 'ar_description', 'time',
            'gold_players', 'silver_players', 'bronze_players', 'special_players',
            'gem', 'coins', 'xp', 'price', 'swap', 'swap_power', 'gem_cost',
            'image', 'open_image', 'en_swap_trade_in_desc', 'ar_swap_trade_in_desc',
            'en_swap_buy_desc', 'ar_swap_buy_desc'
        ]);
    }

    public function update()
    {
        $this->validate();

        $record = BoxesType::findOrFail($this->editingId);

        // 🖼️ Handle Main Image
        if ($this->image && !is_string($this->image)) {
            if ($record->image && File::exists(public_path('storage/' . $record->image))) {
                File::delete(public_path('storage/' . $record->image));
            }
            $record->image = $this->image->store('boxes', 'public');
        }

        // 🖼️ Handle Open Image
        if ($this->open_image && !is_string($this->open_image)) {
            if ($record->open_image && File::exists(public_path('storage/' . $record->open_image))) {
                File::delete(public_path('storage/' . $record->open_image));
            }
            $record->open_image = $this->open_image->store('boxes', 'public');
        }

        // ✏️ Update All Other Fields
        $record->update([
            'en_name' => $this->en_name,
            'en_description' => $this->en_description,
            'ar_description' => $this->ar_description,
            'time' => $this->time,
            'gold_players' => $this->gold_players,
            'silver_players' => $this->silver_players,
            'bronze_players' => $this->bronze_players,
            'special_players' => $this->special_players,
            'gem' => $this->gem,
            'coins' => $this->coins,
            'xp' => $this->xp,
            'price' => $this->price,
            'swap' => $this->swap ?? 0,
            'swap_power' => $this->swap_power,
            'gem_cost' => $this->gem_cost,
            'en_swap_trade_in_desc' => $this->en_swap_trade_in_desc,
            'ar_swap_trade_in_desc' => $this->ar_swap_trade_in_desc,
            'en_swap_buy_desc' => $this->en_swap_buy_desc,
            'ar_swap_buy_desc' => $this->ar_swap_buy_desc,
            'image' => $record->image,
            'open_image' => $record->open_image,
        ]);

        $this->cancelEdit();
        session()->flash('success', '💾 Box Type updated successfully!');
    }


    public function render()
    {
        $records = BoxesType::when($this->search, function ($q) {
            $q->where('en_name', 'like', "%{$this->search}%");
        })
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.admin.boxes-types-table', compact('records'));
    }
}
