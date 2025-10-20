<?php

namespace App\Models;

use App\Traits\FilterByTeam;
use App\Traits\LoggableEntity;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * @package App
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $remember_token
 * @property string $team
 */
class User extends Authenticatable
{
    use Notifiable, FilterByTeam, LoggableEntity;

    protected $fillable = ['name', 'email', 'password', 'remember_token', 'role_id'];
    protected $hidden = ['password', 'remember_token'];

    /**
     * Hash password
     * @param $input
     */
    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setRoleIdAttribute($input)
    {
        $this->attributes['role_id'] = $input ? $input : null;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains('name', $permission);
    }

    public function assignPermission(string $permission)
    {
        $permissionModel = Permission::firstOrCreate(['name' => $permission]);
        $this->permissions()->syncWithoutDetaching($permissionModel);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isTeamAdmin()
    {
        return $this->role_id === 3;
    }

    public function managedTeams()
    {
        return $this->hasMany(Team::class, 'manager_id');
    }

    public function team()
    {
        return $this->teams()->first();
    }

    public function teamName()
    {
        $team = $this->team();
        return $team ? $team->name : null;
    }

    public function isManager()
    {
        return $this->managedTeams()->exists();
    }

    public function getManager()
    {
        return $this->teams()->with('manager')->get()->pluck('manager')->first();
    }

    public function getManagers()
    {
        return $this->teams()->with('manager')->get()->pluck('manager');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function getSubordinates()
    {
        return User::whereHas('teams', function($query) {
            $query->where('manager_id', $this->id);
        })->where('id', '!=', $this->id);
    }

    public function leadingProjects()
    {
        return $this->hasMany(Project::class, 'team_leader_user_id');
    }

    public function activeProjects()
    {
        return $this->hasMany(Project::class, 'team_leader_user_id')
                        ->where('status', 2);
    }

    public function completedProjects()
    {
        return $this->hasMany(Project::class, 'team_leader_user_id')
                        ->where('status', 3);
    }

    public function nextDeadline()
    {
        $nextProject = $this->leadingProjects()
                        ->where('endDate', '>=', now())
                        ->orderBy('endDate', 'asc')
                        ->first();
        return $nextProject ? $nextProject->endDate : null;
    }

    public function isAnyManager(): bool
    {
        return $this->isManager() || $this->isAdmin();
    }

    /**
     * Check if user can approve weekly plans
     */
    public function canApprovePlans(): bool
    {
        return $this->isAnyManager() || $this->isAdmin();
    }

    /**
     * Get all teams the user manages (for filtering)
     */
    public function managedTeamIds(): array
    {
        if ($this->isAdmin()) {
            return Team::pluck('id')->toArray();
        }
        
        return $this->isManager()
            ? $this->managedTeams()->pluck('teams.id')->toArray()
            : [];
    }

    /**
     * Check if the current user can manage another user's plans/tasks.
     * @param User $userToManage The user whose plans/tasks are being checked for management.
     * @return bool
     */
    public function canManage(User $userToManage): bool
    {
        // Admin can manage anyone
        if ($this->isAdmin()) {
            return true;
        }

        // A user cannot manage themselves in this context
        if ($this->id === $userToManage->id) {
            return false;
        }

        // Get IDs of teams managed by the current user (the potential manager)
        $managedTeamIds = $this->managedTeams()->pluck('teams.id');

        // Get IDs of teams the userToManage belongs to
        $userToManageTeamIds = $userToManage->teams()->pluck('teams.id');

        // Check if there's any intersection between the two sets of team IDs
        return $managedTeamIds->intersect($userToManageTeamIds)->isNotEmpty();
    }

    // Weekly plans created by this user
    public function weeklyPlans()
    {
        return $this->hasMany(WeeklyPlan::class);
    }

    // Plans approved by this user (as manager)
    public function approvedPlans()
    {
        return $this->hasMany(WeeklyPlan::class, 'approved_by');
    }

    // Plans rejected by this user (as manager)
    public function rejectedPlans()
    {
        return $this->hasMany(WeeklyPlan::class, 'rejected_by');
    }

    /**
     * Check if user can approve a specific weekly plan
     */
    public function canApprove(WeeklyPlan $plan): bool
    {
        // Admin can approve any plan
        if ($this->isAdmin()) {
            return true;
        }

        // Manager can approve plans from their team members
        if ($this->isManager()) {
            return $this->canManage($plan->user);
        }

        return false;
    }

    /**
     * Check if user can reject a specific weekly plan
     */
    public function canReject(WeeklyPlan $plan): bool
    {
        return $this->canApprove($plan);
    }

    /**
     * Get team members that this user manages (excluding themselves)
     */
    public function getManagedUsers()
    {
        if ($this->isAdmin()) {
            return User::where('id', '!=', $this->id)->get();
        }

        if ($this->isManager()) {
            $managedTeamIds = $this->managedTeams()->pluck('id');
            return User::whereHas('teams', function($query) use ($managedTeamIds) {
                $query->whereIn('teams.id', $managedTeamIds);
            })->where('id', '!=', $this->id)->get();
        }

        return collect();
    }


}
