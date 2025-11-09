<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UserCard;
use App\Models\User;
use App\Models\PlayersCard;
use App\Models\Leagues;
use App\Models\Positions;

class UserCardsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null;
    public $showCreateForm = false;

    // form fields
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

    /** reset pagination on search */
    public function updatingSearch() { $this->resetPage(); }

    /** create new */
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

    /** edit */
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

    /** update */
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

    /** delete */
    public function delete($id)
    {
        UserCard::findOrFail($id)->delete();
        session()->flash('success', '🗑️ Record deleted successfully!');
    }

    private function resetForm()
    {
        $this->reset(['editingId','user_id','card_id','league_id','position_id','is_in_team','is_sub','in_stad','showCreateForm']);
    }

    public function render()
    {
        $records = UserCard::with(['user','card','league','position'])
            ->when($this->search, fn($q) =>
            $q->whereHas('user', fn($u) => $u->where('name','like',"%{$this->search}%"))
                ->orWhereHas('card', fn($c) => $c->where('id',$this->search))
            )
            ->orderBy('id','desc')
            ->paginate(10);

        return view('livewire.admin.user-cards-table', [
            'records'   => $records,
            'users'     => User::orderBy('id')->limit(50)->get(),
            'cards'     => PlayersCard::orderBy('id','desc')->limit(50)->get(),
            'leagues'   => Leagues::orderBy('en_name')->get(),
            'positions' => Positions::orderBy('id')->get(),
        ]);
    }
}
