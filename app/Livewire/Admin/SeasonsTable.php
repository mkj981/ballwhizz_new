<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Seasons;
use App\Models\Leagues;

class SeasonsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;

    public $league_id, $tie_breaker_rule_id, $name, $finished, $pending, $is_current, $starting_at, $ending_at, $standings_recalculated_at, $status;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'league_id' => 'required|integer',
        'name' => 'required|string|max:255',
        'tie_breaker_rule_id' => 'nullable|integer',
        'starting_at' => 'nullable|date',
        'ending_at' => 'nullable|date',
        'standings_recalculated_at' => 'nullable|date',
        'finished' => 'boolean',
        'pending' => 'boolean',
        'is_current' => 'boolean',
        'status' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ✅ Toggle season status
    public function toggleStatus($id)
    {
        $season = Seasons::findOrFail($id);
        $season->status = !$season->status;
        $season->save();

        session()->flash('success', 'Season status updated successfully!');
    }

    // ✅ Toggle is_current flag
    public function toggleCurrent($id)
    {
        $season = Seasons::findOrFail($id);
        $season->is_current = !$season->is_current;
        $season->save();

        session()->flash('success', 'Season current status updated successfully!');
    }

    public function edit($id)
    {
        $record = Seasons::findOrFail($id);
        $this->editingId = $record->id;
        $this->league_id = $record->league_id;
        $this->tie_breaker_rule_id = $record->tie_breaker_rule_id;
        $this->name = $record->name;
        $this->finished = $record->finished;
        $this->pending = $record->pending;
        $this->is_current = $record->is_current;
        $this->starting_at = $record->starting_at;
        $this->ending_at = $record->ending_at;
        $this->standings_recalculated_at = $record->standings_recalculated_at;
        $this->status = $record->status;
    }

    public function cancelEdit()
    {
        $this->reset(['editingId', 'league_id', 'tie_breaker_rule_id', 'name', 'finished', 'pending', 'is_current', 'starting_at', 'ending_at', 'standings_recalculated_at', 'status']);
    }

    public function update()
    {
        $this->validate();

        Seasons::where('id', $this->editingId)->update([
            'league_id' => $this->league_id,
            'tie_breaker_rule_id' => $this->tie_breaker_rule_id,
            'name' => $this->name,
            'finished' => $this->finished ?? 0,
            'pending' => $this->pending ?? 0,
            'is_current' => $this->is_current ?? 0,
            'starting_at' => $this->starting_at,
            'ending_at' => $this->ending_at,
            'standings_recalculated_at' => $this->standings_recalculated_at,
            'status' => $this->status ?? 0,
        ]);

        $this->cancelEdit();
        session()->flash('success', 'Season updated successfully!');
    }

    public function render()
    {
        $records = Seasons::with('league')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhereHas('league', function ($leagueQuery) {
                            $leagueQuery->where('en_name', 'like', "%{$this->search}%")
                                ->orWhere('ar_name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->orderBy('id', 'ASC')
            ->paginate(10);

        $leagues = Leagues::pluck('en_name', 'id');

        return view('livewire.admin.seasons-table', compact('records', 'leagues'));
    }
}
