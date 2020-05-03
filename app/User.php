<?php

namespace App;

use App\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property mixed id
 * @method static first()
 * @method static whereHas(string $string, \Closure $param)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idn', 'idn_type', 'last_name', 'name', 'email', 'phone', 'address', 'password'
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $with = ['roles'];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * @return mixed
     */
    public function getFullNameAttribute(){
        return $this->name.' '.$this->last_name;
    }

    /**
     * The roles that belong to the user.
     */

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Set role to user.
     * @param string $roleName
     * @return void
     */
    public function setRole(string $roleName)
    {
        return $this->roles()->attach($roleName);
    }

    /**
     * Check if user Have Role Assigned.
     */
    public function hasRole(string $roleName)
    {
        return $this->roles->pluck('name_id')->contains($roleName);
    }
    /**
     * @return BelongsToMany
     */
    public function accounts()
    {
        return $this->belongsToMany(Account::class);
    }
    /**
     * @return BelongsToMany
     */
    public function receivers()
    {
        return $this->belongsToMany(self::class, 'users_receivers', 'user_id', 'receiver_id')->withTimestamps();
    }
    /**
     * @return BelongsToMany
     */
    public function senders()
    {
        return $this->belongsToMany(self::class, 'users_receivers', 'receiver_id', 'user_id')->withTimestamps();
    }
}
