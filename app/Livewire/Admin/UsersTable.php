<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class UsersTable extends Component
{
    use WithPagination;

    public string $search = '';
    protected string $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) =>
            $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('social_type', 'like', "%{$this->search}%")
                ->orWhere('uid', 'like', "%{$this->search}%")
            )
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.admin.users-table', compact('users'));
    }
}
