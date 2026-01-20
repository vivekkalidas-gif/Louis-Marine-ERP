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
            return $this->roles->contains('slug', $role);
        }
        
        // Convert array to collection if needed
        if (is_array($role)) {
            $role = collect($role);
        }
        
        return !! $role->intersect($this->roles->pluck('slug'))->count();
    }

    /**
     * Check if user has a specific permission via their roles
     */
    public function hasPermissionTo($permissionSlug)
    {
        // Super Admin Bypass is handled in AppServiceProvider Gate::before, 
        // but for direct method calls it's good to confirm here too if Gate isn't used.
        // However, standardizing on Gate::check() is better. 
        // For this method, we check the actual assignments.
        
        // 1. Check if ANY of the user's roles has the permission
        // We iterate through roles and their permissions.
        // To avoid N+1, accessing $this->roles should have them loaded, 
        // or we use a more optimized query if needed. 
        // For current scale, eager loading 'permissions' on roles or simple iteration is fine.
        
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('slug', $permissionSlug)) {
                return true;
            }
        }
        
        return false;
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
