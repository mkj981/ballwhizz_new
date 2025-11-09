<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WeekMonth;

class WeekMonthsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null;

    public $week_name, $week, $start_date, $end_date;

    public $showCreateForm = false;

    protected $rules = [
        'week_name' => 'required|string|max:255',
        'week' => 'required|integer|min:1',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ];


    /** 🔄 Reset pagination when searching */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /** ✏️ Edit record */
    public function edit($id)
    {
        $record = WeekMonth::findOrFail($id);
        $this->editingId = $record->id;
        $this->week_name = $record->week_name;
        $this->week = $record->week;
        $this->start_date = $record->start_date->format('Y-m-d H:i:s');
        $this->end_date = $record->end_date->format('Y-m-d H:i:s');
        $this->showCreateForm = false;
    }

    /** ❌ Cancel edit or create */
    public function cancelEdit()
    {
        $this->reset(['editingId', 'week_name', 'week', 'start_date', 'end_date', 'showCreateForm']);
    }

    public function create()
    {
        $this->validate();

        // 🕐 Normalize dates to full-day boundaries
        $start = date('Y-m-d 00:00:00', strtotime($this->start_date));
        $end   = date('Y-m-d 23:59:59', strtotime($this->end_date));

        // 🗓️ Create the new week record
        $week = WeekMonth::create([
            'week_name'  => $this->week_name,
            'week'       => $this->week,
            'start_date' => $start,
            'end_date'   => $end,
        ]);

        // ⚽ Automatically import matches for this week
        try {
            Artisan::call('import:prediction-matches-sportmonks', [
                '--start_date' => date('Y-m-d', strtotime($this->start_date)),
                '--end_date'   => date('Y-m-d', strtotime($this->end_date)),
                '--lang'       => 'en',
            ]);

            session()->flash('success', '✅ New week created and matches imported successfully!');
        } catch (\Exception $e) {
            session()->flash('error', '⚠️ Week created, but match import failed: ' . $e->getMessage());
        }

        // 🧹 Reset form state
        $this->cancelEdit();
    }


    public function update()
    {
        $this->validate();

        $record = WeekMonth::findOrFail($this->editingId);
        $start = date('Y-m-d 00:00:00', strtotime($this->start_date));
        $end = date('Y-m-d 23:59:00', strtotime($this->end_date));

        $record->update([
            'week_name' => $this->week_name,
            'week' => $this->week,
            'start_date' => $start,
            'end_date' => $end,
        ]);

        $this->cancelEdit();
        session()->flash('success', 'Week updated successfully!');
    }


    /** 🗑 Delete record */
    public function delete($id)
    {
        $record = WeekMonth::findOrFail($id);
        $record->delete();

        session()->flash('success', 'Week deleted successfully!');
    }

    public function render()
    {
        $records = WeekMonth::query()
            ->when($this->search, fn($q) =>
            $q->where('week_name', 'like', "%{$this->search}%")
                ->orWhere('week', 'like', "%{$this->search}%")
            )
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return view('livewire.admin.week-months-table', compact('records'));
    }
}
