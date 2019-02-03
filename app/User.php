<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }
    /**
     * Toogle role to user
     */
    public function toogleRole(String $roleName)
    {
        return $this->roles()->toggle($roleName);
    }
    /**
     * Set role to user
     */
    public function setRole(String $roleName)
    {
        return $this->roles()->attach($roleName);
    }
    /**
     * Check if user Have Role Assigned
     */
    public function hasRole(String $roleName)
    {
        return $this->roles->pluck('name')->contains($roleName);
    }
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
