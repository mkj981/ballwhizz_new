<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Leagues;
use App\Models\Seasons;

class SeasonTeams extends Component
{
    public $league_id = null;
    public $season_id = null;

    public $leagues;
    public $seasons;
    public $teams;

    public function mount()
    {
        // Load all leagues initially
        $this->leagues = Leagues::orderBy('id', 'asc')->get(['id', 'en_name']);
        $this->seasons = collect();
        $this->teams = collect();
    }

    /** When league changes → load its seasons */
    public function updatedLeagueId($value)
    {
        $this->season_id = null;
        $this->teams = collect();

        $this->seasons = $value
            ? Seasons::where('league_id', $value)
                ->orderBy('id', 'asc')
                ->get(['id', 'name'])
            : collect();
    }

    /** When season changes → load its teams */
    public function updatedSeasonId($value)
    {
        $this->teams = collect();

        if ($value) {
            $season = Seasons::with(['teams.country'])->find($value);
            $this->teams = $season?->teams ?? collect();

            if ($this->teams->isEmpty()) {
                $this->dispatchBrowserEvent('notify', [
                    'message' => 'No teams found for this season.',
                    'type' => 'warning',
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.season-teams', [
            'teamsCount' => $this->teams->count(),
        ]);
    }
}
