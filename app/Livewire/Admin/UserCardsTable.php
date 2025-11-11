<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UserCard;
use App\Models\User;
use App\Models\PlayersCard;
use App\Models\Leagues;
use App\Models\Positions;
use Illuminate\Support\Facades\DB;

class UserCardsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null;
    public $showCreateForm = false;

    // Sorting
    public $sortField = 'user_cards.id';
    public $sortDirection = 'desc';

    // Form fields
    public $user_id, $card_id, $league_id, $position_id, $is_in_team = false, $is_sub = false, $in_stad = 0;

    protected $rules = [
        'user_id'     => 'required|integer|exists:users,id',
        'card_id'     => 'required|integer|exists:players_cards,id',
        'league_id'   => 'required|integer|exists:leagues,id',
        'position_id' => 'nullable|integer|exists:positions,id',
        'is_in_team'  => 'boolean',
        'is_sub'      => 'boolean',
        'in_stad'     => 'integer|min:0',
    ];

    /** 🔍 Reset pagination when search changes */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /** ↕️ Toggle sorting */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /** ➕ Create new record */
    public function store()
    {
        $this->validate();

        UserCard::create([
            'user_id'     => $this->user_id,
            'card_id'     => $this->card_id,
            'league_id'   => $this->league_id,
            'position_id' => $this->position_id ?: null,
            'is_in_team'  => $this->is_in_team,
            'is_sub'      => $this->is_sub,
            'in_stad'     => $this->in_stad ?? 0,
        ]);

        $this->resetForm();
        session()->flash('success', '✅ User card added successfully!');
    }

    /** ✏️ Edit record */
    public function edit($id)
    {
        $record = UserCard::findOrFail($id);
        $this->editingId   = $record->id;
        $this->user_id     = $record->user_id;
        $this->card_id     = $record->card_id;
        $this->league_id   = $record->league_id;
        $this->position_id = $record->position_id;
        $this->is_in_team  = $record->is_in_team;
        $this->is_sub      = $record->is_sub;
        $this->in_stad     = $record->in_stad;
        $this->showCreateForm = false;
    }

    /** 💾 Update record */
    public function update()
    {
        $this->validate();

        $record = UserCard::findOrFail($this->editingId);
        $record->update([
            'user_id'     => $this->user_id,
            'card_id'     => $this->card_id,
            'league_id'   => $this->league_id,
            'position_id' => $this->position_id ?: null,
            'is_in_team'  => $this->is_in_team,
            'is_sub'      => $this->is_sub,
            'in_stad'     => $this->in_stad ?? 0,
        ]);

        $this->resetForm();
        session()->flash('success', '✅ Record updated successfully!');
    }

    /** 🗑️ Delete record */
    public function delete($id)
    {
        UserCard::findOrFail($id)->delete();
        session()->flash('success', '🗑️ Record deleted successfully!');
    }

    /** ♻️ Reset form */
    private function resetForm()
    {
        $this->reset([
            'editingId', 'user_id', 'card_id', 'league_id', 'position_id',
            'is_in_team', 'is_sub', 'in_stad', 'showCreateForm'
        ]);
    }

    /** 🧾 Render table */
    public function render()
    {
        $records = UserCard::query()
            ->join('players_cards', 'user_cards.card_id', '=', 'players_cards.id')
            ->join('players', 'players_cards.player_id', '=', 'players.id')
            ->leftJoin('users', 'user_cards.user_id', '=', 'users.id')
            ->leftJoin('leagues', 'user_cards.league_id', '=', 'leagues.id')
            ->leftJoin('positions', 'user_cards.position_id', '=', 'positions.id')
            ->select('user_cards.*', 'players.en_common_name as player_name', 'users.name as user_name', 'leagues.en_name as league_name', 'positions.en_name as position_name')
            ->when($this->search, function ($query) {
                $query->where('users.name', 'like', "%{$this->search}%")
                    ->orWhere('players.en_common_name', 'like', "%{$this->search}%")
                    ->orWhere('players.en_name', 'like', "%{$this->search}%")
                    ->orWhere('user_cards.id', $this->search);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.user-cards-table', [
            'records'   => $records,
            'users'     => User::orderBy('id')->limit(50)->get(),
            'cards'     => PlayersCard::with('player')->orderBy('id', 'desc')->limit(50)->get(),
            'leagues'   => Leagues::orderBy('en_name')->get(),
            'positions' => Positions::orderBy('id')->get(),
        ]);
    }
}
