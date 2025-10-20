<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Team
 *
 * @package App
 * @property string $name
 * @property int $manager_id
 */
class Team extends Model
{
    use LoggableEntity;

    protected $fillable = ['name', 'manager_id', 'description'];
    protected $hidden = [];

    /**
     * Users that belong to this team (many-to-many)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user');
    }

    /**
     * Manager of this team
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get team members excluding the manager
     */
    public function membersExcludingManager()
    {
        return $this->users()->where('users.id', '!=', $this->manager_id);
    }

    /**
     * Get all team members including the manager
     */
    public function allMembers()
    {
        return $this->users();
    }
}
