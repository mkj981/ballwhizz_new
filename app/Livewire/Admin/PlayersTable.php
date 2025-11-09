<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use App\Models\Player;

class PlayersTable extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null;

    public $en_common_name, $ar_common_name, $image, $image_path;

    protected $rules = [
        'en_common_name' => 'required|string|max:255',
        'ar_common_name' => 'nullable|string|max:255',
        'image'          => 'nullable|image|max:2048', // max 2MB
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $record = Player::findOrFail($id);
        $this->editingId = $record->id;
        $this->en_common_name = $record->en_common_name;
        $this->ar_common_name = $record->ar_common_name;
        $this->image_path = $record->image_path;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'en_common_name', 'ar_common_name', 'image', 'image_path']);
    }

    public function update()
    {
        $this->validate();

        $record = Player::find($this->editingId);
        if (!$record) return;

        $updateData = [
            'en_common_name' => $this->en_common_name,
            'ar_common_name' => $this->ar_common_name,
        ];

        // ✅ Handle image upload (to storage/images)
        if ($this->image) {
            // Delete old image if exists
            if ($record->image_path && File::exists(public_path('storage/' . $record->image_path))) {
                File::delete(public_path('storage/' . $record->image_path));
            }

            // Save new image to storage/app/public/images
            $filename = 'player_' . time() . '.' . $this->image->getClientOriginalExtension();
            $path = $this->image->storeAs('images', $filename, 'public');

            $updateData['image_path'] = $path; // Save as "images/player_123.png"
        }

        $record->update($updateData);

        session()->flash('success', '✅ Player updated successfully!');
        $this->cancelEdit();
    }

    public function delete($id)
    {
        $record = Player::find($id);
        if ($record) {
            if ($record->image_path && File::exists(public_path('storage/' . $record->image_path))) {
                File::delete(public_path('storage/' . $record->image_path));
            }
            $record->delete();
            session()->flash('success', '🗑️ Player deleted successfully!');
        }
    }

    public function render()
    {
        $records = Player::with(['league', 'team'])
            ->when($this->search, function ($query) {
                $query->where('en_common_name', 'like', "%{$this->search}%")
                    ->orWhere('ar_common_name', 'like', "%{$this->search}%")
                    ->orWhere('display_name', 'like', "%{$this->search}%");
            })
            ->orderBy('team_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->paginate(10);

        return view('livewire.admin.players-table', compact('records'));
    }

}
