<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'role_id'
    ];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Relationship: each admin belongs to one role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the admin has a given role name (supports both string + relationship)
     */
    public function hasRole($role): bool
    {
        if ($this->role_id && $this->role) {
            return $this->role->name === $role;
        }

        return $this->role === $role;
    }

    /**
     * Check if admin has any of multiple roles
     */
    public function hasAnyRole(array $roles): bool
    {
        if ($this->role_id && $this->role) {
            return in_array($this->role->name, $roles);
        }

        return in_array($this->role, $roles);
    }
}
