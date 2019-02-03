<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];
    protected $primaryKey = 'name';
    public $incrementing = false;

    /**
     * The roles that belong to the user.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
