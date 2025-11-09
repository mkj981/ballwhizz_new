<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CardsWeek;
use App\Models\Leagues;
use App\Models\WeekMonth;

class CardsWeeksTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔍 Search + UI states
    public $search = '';
    public $editingId = null;
    public $showCreateForm = false;

    // 🧩 Form fields
    public $week_months_id, $league_id, $matchday, $start, $end, $close_at, $is_active = false, $is_open = false;

    // 🧾 Validation
    protected $rules = [
        'week_months_id' => 'required|integer|exists:week_months,id',
        'league_id'      => 'required|integer|exists:leagues,id',
        'matchday'       => 'nullable|integer|min:1',
        'start'          => 'nullable|date',
        'end'            => 'nullable|date|after_or_equal:start',
        'close_at'       => 'nullable|date|after_or_equal:end',
        'is_active'      => 'boolean',
        'is_open'        => 'boolean',
    ];

    /** 🔄 Reset pagination on search */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /** ✏️ Edit record */
    public function edit($id)
    {
        $record = CardsWeek::findOrFail($id);

        $this->editingId     = $record->id;
        $this->week_months_id = $record->week_months_id;
        $this->league_id     = $record->league_id;
        $this->matchday      = $record->matchday;
        $this->start         = $record->start ? date('Y-m-d\TH:i', strtotime($record->start)) : null;
        $this->end           = $record->end ? date('Y-m-d\TH:i', strtotime($record->end)) : null;
        $this->close_at      = $record->close_at ? date('Y-m-d\TH:i', strtotime($record->close_at)) : null;
        $this->is_active     = (bool) $record->is_active;
        $this->is_open       = (bool) $record->is_open;

        $this->showCreateForm = false; // hide create form while editing
    }

    /** ❌ Cancel edit or create */
    public function cancelEdit()
    {
        $this->reset([
            'editingId',
            'week_months_id',
            'league_id',
            'matchday',
            'start',
            'end',
            'close_at',
            'is_active',
            'is_open',
            'showCreateForm',
        ]);
    }

    /** 💾 Update record */
    public function update()
    {
        $this->validate();

        // ✅ Check that selected league is active (cards_status = 1)
        $league = Leagues::activeCards()->find($this->league_id);
        if (!$league) {
            session()->flash('error', '⚠️ Selected league is not active for Cardz.');
            return;
        }

        $record = CardsWeek::findOrFail($this->editingId);
        $record->update([
            'week_months_id' => $this->week_months_id,
            'league_id'      => $this->league_id,
            'matchday'       => $this->matchday,
            'start'          => $this->start,
            'end'            => $this->end,
            'close_at'       => $this->close_at,
            'is_active'      => $this->is_active ? 1 : 0,
            'is_open'        => $this->is_open ? 1 : 0,
        ]);

        $this->cancelEdit();
        session()->flash('success', '✅ Week updated successfully!');
    }

    /** 🆕 Create record */
    public function store()
    {
        $this->validate();

        // ✅ Check that selected league is active
        $league = Leagues::activeCards()->find($this->league_id);
        if (!$league) {
            session()->flash('error', '⚠️ You can only create weeks for leagues with active Cardz.');
            return;
        }

        CardsWeek::create([
            'week_months_id' => $this->week_months_id,
            'league_id'      => $this->league_id,
            'matchday'       => $this->matchday,
            'start'          => $this->start,
            'end'            => $this->end,
            'close_at'       => $this->close_at,
            'is_active'      => $this->is_active ? 1 : 0,
            'is_open'        => $this->is_open ? 1 : 0,
        ]);

        $this->cancelEdit();
        session()->flash('success', '✅ Week created successfully!');
    }

    /** 🗑 Delete record */
    public function delete($id)
    {
        $record = CardsWeek::findOrFail($id);
        $record->delete();

        session()->flash('success', '🗑️ Week deleted successfully!');
    }

    /** 🎨 Render */
    public function render()
    {
        $records = CardsWeek::with(['league', 'weekMonth'])
            ->when($this->search, function ($query) {
                $query->whereHas('league', fn($q) =>
                $q->where('en_name', 'like', "%{$this->search}%")
                )->orWhereHas('weekMonth', fn($q) =>
                $q->where('week_name', 'like', "%{$this->search}%")
                );
            })
            ->orderBy('start', 'desc')
            ->paginate(10);

        return view('livewire.admin.cards-weeks-table', [
            'records'     => $records,
            'leagues'     => Leagues::activeCards()->orderBy('en_name')->get(),
            'weekMonths'  => WeekMonth::orderBy('id')->get(),
        ]);
    }
}
