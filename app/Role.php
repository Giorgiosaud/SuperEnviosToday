<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class Role extends Model
{
    protected $fillable = ['name_id', 'name'];
    protected $primaryKey = 'name_id';
    public $incrementing = false;

    /**
     * The roles that belong to the user.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
    //
}
