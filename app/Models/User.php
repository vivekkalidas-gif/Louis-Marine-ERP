<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, SoftDeletes;

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps(); // Direct relation if needed, or through roles
    }

    public function employeeDetail()
    {
        return $this->hasOne(EmployeeDetail::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }

    public function manager()
    {
        // Manager link is stored in employeeDetail via user_id
        // But the relationship is: User -> hasOne EmployeeDetail -> belongsTo Manager (User)
        return $this->hasOneThrough(User::class, EmployeeDetail::class, 'user_id', 'id', 'id', 'reporting_manager_id');
    }

    // Direct access to reporting manager via employee detail
    public function reportingManager()
    {
        return $this->employeeDetail->manager ?? null;
    }

    public function subordinates()
    {
        return $this->hasManyThrough(User::class, EmployeeDetail::class, 'reporting_manager_id', 'id', 'id', 'user_id');
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            if ($this->relationLoaded('roles')) {
                return $this->roles->contains('slug', $role);
            }
            return $this->roles()->where('slug', $role)->exists();
        }
        
        if (is_array($role)) {
            $role = collect($role);
        }
        
        if ($this->relationLoaded('roles')) {
            return !! $role->intersect($this->roles->pluck('slug'))->count();
        }
        
        return $this->roles()->whereIn('slug', $role)->exists();
    }

    /**
     * Check if user has a specific permission via their roles
     */
    public function hasPermissionTo($permissionSlug)
    {
        if ($this->hasRole('super-admin') || $this->hasRole('admin')) {
            return true;
        }

        if ($this->relationLoaded('roles')) {
            foreach ($this->roles as $role) {
                if ($role->relationLoaded('permissions')) {
                    if ($role->permissions->contains('slug', $permissionSlug)) {
                        return true;
                    }
                }
            }
        }

        return $this->roles()->whereHas('permissions', function ($q) use ($permissionSlug) {
            $q->where('slug', $permissionSlug);
        })->exists();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'avatar',
        'password',
        'is_active',
        'last_login_at',
        'signature_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Scope a query to only include users with a given role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $roles
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole($query, $roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return $query->whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('slug', $roles);
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
