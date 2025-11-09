<?php

namespace App\Livewire\Admin;

use App\Models\Player;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PredictionCardsMatches;
use App\Models\PredictionCardsMatchScorer;
use App\Models\Leagues;
use App\Models\Teams;


class PredictionCardsMatchesTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null;

    // 🔹 Form fields
    public $league_id, $match_id, $home_team_id, $away_team_id, $home_team_result, $away_team_result, $status;

    // 🔹 Scorers section
    public $scorers = []; // holds scorers for a specific match

    public $showScorers = false;
    public $selectedMatch = null;

    protected $rules = [
        'league_id' => 'required|integer',
        'home_team_id' => 'required|integer',
        'away_team_id' => 'required|integer',
        'home_team_result' => 'nullable|integer',
        'away_team_result' => 'nullable|integer',
        'status' => 'required|integer',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $record = PredictionCardsMatches::findOrFail($id);
        $this->editingId = $record->id;
        $this->league_id = $record->league_id;
        $this->match_id = $record->match_id;
        $this->home_team_id = $record->home_team_id;
        $this->away_team_id = $record->away_team_id;
        $this->home_team_result = $record->home_team_result;
        $this->away_team_result = $record->away_team_result;
        $this->status = $record->status;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'league_id', 'match_id', 'home_team_id', 'away_team_id', 'home_team_result', 'away_team_result', 'status']);
    }

    public function update()
    {
        $this->validate();
        $record = PredictionCardsMatches::findOrFail($this->editingId);

        $record->update([
            'league_id' => $this->league_id,
            'match_id' => $this->match_id,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'home_team_result' => $this->home_team_result,
            'away_team_result' => $this->away_team_result,
            'status' => $this->status,
        ]);

        $this->cancelEdit();
        session()->flash('success', 'Match updated successfully!');
    }

    public function delete($id)
    {
        $record = PredictionCardsMatches::findOrFail($id);
        $record->delete();
        session()->flash('success', 'Match deleted successfully!');
    }

    // 🔹 View and manage scorers for a match
    public function showScorers($matchId)
    {
        $this->selectedMatch = PredictionCardsMatches::with(['scorers.player'])->findOrFail($matchId);
        $this->scorers = $this->selectedMatch->scorers->toArray();
        $this->showScorers = true;
    }

    public function closeScorers()
    {
        $this->showScorers = false;
        $this->selectedMatch = null;
        $this->scorers = [];
    }

    public function render()
    {
        $records = PredictionCardsMatches::with(['league', 'homeTeam', 'awayTeam'])
            ->when($this->search, function ($query) {
                $query->whereHas('homeTeam', fn($q) =>
                $q->where('en_name', 'like', "%{$this->search}%")
                    ->orWhere('ar_name', 'like', "%{$this->search}%")
                )->orWhereHas('awayTeam', fn($q) =>
                $q->where('en_name', 'like', "%{$this->search}%")
                    ->orWhere('ar_name', 'like', "%{$this->search}%")
                );
            })
            ->orderBy('starting_at', 'ASC') // ✅ Order by match start time (latest first)
            ->paginate(10);

        return view('livewire.admin.prediction-cards-matches-table', [
            'records' => $records,
            'leagues' => Leagues::all(),
            'teams' => Teams::all(),
            'players' => Player::all(),
        ]);
    }


}
