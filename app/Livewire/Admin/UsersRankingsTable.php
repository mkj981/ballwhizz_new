<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UsersRanking;
use App\Models\WeekMonth;
use Illuminate\Support\Facades\DB;

class UsersRankingsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $type = 'all';       // all, cards, trivia, prediction
    public $month = null;       // months 8 → 12 then 1 → 7
    public $week_id = null;     // FK to week_months table

    public function updating($field)
    {
        // Prevent selecting both filters at the same time
        if ($field === 'month' && $this->month !== null) {
            $this->week_id = null;
        }

        if ($field === 'week_id' && $this->week_id !== null) {
            $this->month = null;
        }

        $this->resetPage();
    }

    public function getQuery()
    {
        $query = UsersRanking::query()
            ->select(
                'user_id',
                DB::raw('SUM(points) as total_points')
            )
            ->groupBy('user_id')
            ->with('user');

        /*
        |--------------------------------------------------------------------------
        | SEARCH FILTER (username/email/name)
        |--------------------------------------------------------------------------
        */
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('username', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | TYPE FILTER
        |--------------------------------------------------------------------------
        */
        if ($this->type !== 'all') {
            $query->where('type', $this->type);
        }

        /*
        |--------------------------------------------------------------------------
        | MONTH FILTER (ONLY IF week_id IS EMPTY)
        |--------------------------------------------------------------------------
        */
        if ($this->month && !$this->week_id) {

            $monthInt = intval($this->month);  // 🔥 FIX: cast to int

            $start = now()->setMonth($monthInt)->startOfMonth();
            $end   = now()->setMonth($monthInt)->endOfMonth();

            $query->whereBetween('game_date', [$start, $end]);
        }

        /*
        |--------------------------------------------------------------------------
        | WEEK FILTER (ONLY IF month IS EMPTY)
        |--------------------------------------------------------------------------
        */
        if ($this->week_id && !$this->month) {

            $week = WeekMonth::find($this->week_id);

            if ($week) {
                // 🔥 FIX: correct column names start_date / end_date
                $query->whereBetween('game_date', [
                    $week->start_date,
                    $week->end_date
                ]);
            }
        }

        return $query;
    }

    public function render()
    {
        $records = $this->getQuery()
            ->orderBy('total_points', 'DESC')
            ->paginate(10);

        // You may want to order by start_date instead of id
        $weeks = WeekMonth::orderBy('start_date', 'asc')->get();

        return view('livewire.admin.users-rankings-table', [
            'records' => $records,
            'weeks'   => $weeks,
        ]);
    }

    public function updatedMonth($value)
    {
        if (!empty($value)) {
            $this->week_id = null; // reset week when month selected
        }
    }

    public function updatedWeekId($value)
    {
        if (!empty($value)) {
            $this->month = null; // reset month when week selected
        }
    }

}
