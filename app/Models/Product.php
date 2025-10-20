<?php
namespace App\Models;

use App\Traits\FilterByUser;
use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Product
 *
 * @package App
 * @property string $name
 * @property string $description
 * @property string $created_by
 * @property string $created_by_team
*/
class Product extends Model
{
    use SoftDeletes, FilterByUser, LoggableEntity;

    protected $fillable = ['name', 'description', 'created_by_id', 'created_by_team_id'];
    protected $hidden = [];

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCreatedByIdAttribute($input)
    {
        $this->attributes['created_by_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCreatedByTeamIdAttribute($input)
    {
        $this->attributes['created_by_team_id'] = $input ? $input : null;
    }
    
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    
    public function created_by_team()
    {
        return $this->belongsTo(Team::class, 'created_by_team_id');
    }
}