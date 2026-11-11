<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => UserRole::class,
        ];
    }

    public function isManager(): bool { return $this->role === UserRole::MANAGER; }
    public function isClient(): bool  { return $this->role === UserRole::CLIENT; }

    public function getRedirectRoute(): string
    {
        return match($this->role) {
            \App\Enums\UserRole::MANAGER => route('manager.index', absolute: false),
            default => route('client-requests.index', absolute: false),
        };
    }

    public function clientRequests(): HasMany
    {
        return $this->hasMany(ClientRequest::class, 'client_id');
    }

    public function assignedRequests(): HasMany
    {
        return $this->hasMany(ClientRequest::class, 'manager_id');
    }
}
