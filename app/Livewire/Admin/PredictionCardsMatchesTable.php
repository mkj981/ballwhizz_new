<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PredictionCardsMatches;
use App\Models\Leagues;
use App\Models\Teams;
use App\Models\Player;
use Carbon\Carbon;

class PredictionCardsMatchesTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔍 Search & State
    public $search = '';
    public $editingId = null;

    // 🏟️ Match fields
    public $league_id, $match_id, $home_team_id, $away_team_id;
    public $home_team_result, $away_team_result, $status;
    public $prediction_calculate, $card_calculate, $starting_at;

    // ⚽ Scorers modal
    public $showScorers = false;
    public $selectedMatch = null;

    protected $rules = [
        'league_id'         => 'required|integer',
        'home_team_id'      => 'required|integer',
        'away_team_id'      => 'required|integer',
        'home_team_result'  => 'nullable|integer',
        'away_team_result'  => 'nullable|integer',
        'status'            => 'required|integer',
    ];

    /** 🔄 Reset pagination when searching */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /** ✏️ Edit a match */
    public function edit($id)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $record = PredictionCardsMatches::findOrFail($id);
        $this->editingId = $record->id;

        $this->league_id = $record->league_id;
        $this->match_id = $record->match_id;
        $this->home_team_id = $record->home_team_id;
        $this->away_team_id = $record->away_team_id;
        $this->home_team_result = $record->home_team_result;
        $this->away_team_result = $record->away_team_result;
        $this->status = $record->status;
        $this->prediction_calculate = $record->prediction_calculate;
        $this->card_calculate = $record->card_calculate;
        $this->starting_at = $record->starting_at
            ? Carbon::parse($record->starting_at)->format('Y-m-d\TH:i')
            : null;
    }

    /** ❌ Cancel editing */
    public function cancelEdit()
    {
        $this->reset([
            'editingId',
            'league_id',
            'match_id',
            'home_team_id',
            'away_team_id',
            'home_team_result',
            'away_team_result',
            'status',
            'starting_at',
            'prediction_calculate',
            'card_calculate',
        ]);
    }

    /** 💾 Update a match */
    public function update()
    {
        $this->validate();

        $record = PredictionCardsMatches::findOrFail($this->editingId);
        $record->update([
            'league_id'            => $this->league_id,
            'match_id'             => $this->match_id,
            'home_team_id'         => $this->home_team_id,
            'away_team_id'         => $this->away_team_id,
            'home_team_result'     => $this->home_team_result,
            'away_team_result'     => $this->away_team_result,
            'status'               => $this->status,
            'starting_at'          => $this->starting_at,
            'prediction_calculate' => $this->prediction_calculate,
            'card_calculate'       => $this->card_calculate,
        ]);

        $this->cancelEdit();
        $this->resetPage();
        session()->flash('success', '✅ Match updated successfully!');
    }

    /** 🗑️ Delete a match */
    public function delete($id)
    {
        PredictionCardsMatches::findOrFail($id)->delete();
        $this->resetPage();
        session()->flash('success', '🗑️ Match deleted successfully!');
    }

    /** 👟 View scorers modal (renamed to avoid Livewire reserved names) */
    public function viewScorers($matchId)
    {
        $this->selectedMatch = PredictionCardsMatches::with([
            'homeTeam',
            'awayTeam',
            'scorers.player',
        ])->findOrFail($matchId);

        $this->showScorers = true;
    }

    /** 🚪 Close scorers modal */
    public function closeScorers()
    {
        $this->showScorers = false;
        $this->selectedMatch = null;
    }

    /** 🎨 Render the component */
    public function render()
    {
        $records = PredictionCardsMatches::with(['league', 'homeTeam', 'awayTeam'])
            ->when($this->search, function ($query) {
                $query->whereHas('homeTeam', function ($q) {
                    $q->where('en_name', 'like', "%{$this->search}%")
                        ->orWhere('ar_name', 'like', "%{$this->search}%");
                })->orWhereHas('awayTeam', function ($q) {
                    $q->where('en_name', 'like', "%{$this->search}%")
                        ->orWhere('ar_name', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('starting_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.prediction-cards-matches-table', [
            'records' => $records,
            'leagues' => Leagues::orderBy('en_name')->get(),
            'teams'   => Teams::orderBy('en_name')->get(),
            'players' => Player::orderBy('en_common_name')->get(),
        ]);
    }
}
