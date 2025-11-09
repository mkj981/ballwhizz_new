<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PlayersCard;
use App\Models\Player;
use App\Models\CardType;

class PlayersCardsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null;

    // Fields
    public $player_id, $type_id, $energy = 10, $week_id, $stats;

    protected $rules = [
        'player_id' => 'required|integer|exists:players,id',
        'type_id' => 'required|integer|exists:card_types,id',
        'energy' => 'required|numeric|min:0|max:100',
        'week_id' => 'nullable|integer',
        'stats' => 'nullable|string',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $record = PlayersCard::findOrFail($id);
        $this->editingId = $record->id;
        $this->player_id = $record->player_id;
        $this->type_id = $record->type_id;
        $this->energy = $record->energy;
        $this->week_id = $record->week_id;
        $this->stats = is_array($record->stats)
            ? json_encode($record->stats, JSON_PRETTY_PRINT)
            : $record->stats;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'player_id', 'type_id', 'energy', 'week_id', 'stats']);
    }

    public function update()
    {
        $this->validate();

        $record = PlayersCard::findOrFail($this->editingId);

        $record->update([
            'player_id' => $this->player_id,
            'type_id' => $this->type_id,
            'energy' => $this->energy,
            'week_id' => $this->week_id,
            'stats' => $this->stats ? json_decode($this->stats, true) : null,
        ]);

        $this->cancelEdit();
        session()->flash('success', '🎴 Player Card updated successfully!');
    }

    public function render()
    {
        $players = Player::orderBy('en_common_name', 'asc')->get(['id', 'en_common_name']);
        $types = CardType::orderBy('id', 'asc')->get(['id', 'en_name']);

        $records = PlayersCard::with(['player', 'type'])
            ->when($this->search, function ($q) {
                $q->whereHas('player', fn($p) =>
                $p->where('en_common_name', 'like', "%{$this->search}%")
                    ->orWhere('ar_common_name', 'like', "%{$this->search}%")
                );
            })
            ->orderBy('energy', 'ASC')
            ->paginate(10);

        return view('livewire.admin.players-cards-table', compact('records', 'players', 'types'));
    }
}
