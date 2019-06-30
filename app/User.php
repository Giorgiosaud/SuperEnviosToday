<?php

namespace App;

use App\Observers\UserObserver;
use Eloquent;
use App\Contracts\CanResetPassword;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Passport\Client;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;

/**
 * Class User
 *
 * @package App
 * @property int $id
 * @property string $name
 * @property string|null $last_name
 * @property string $idn
 * @property string $idn_type
 * @property string|null $email
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Account[] $accounts
 * @property-read Collection|Client[] $clients
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read Collection|User[] $receivers
 * @property-read Collection|Role[] $roles
 * @property-read Collection|User[] $senders
 * @property-read Collection|Token[] $tokens
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAddress($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIdn($value)
 * @method static Builder|User whereIdnType($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 */
class User extends Authenticatable implements CanResetPassword
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idn', 'idn_type', 'name', 'last_name', 'email', 'password', 'address', 'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot'
    ];

    /**
     * @var array
     */
    protected $with = ['roles', 'accounts'];

    /**
     *
     */
    public static function boot()
    {
        parent::boot();
        User::observe(new UserObserver);
    }
    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Toogle role to user.
     */
    public function toogleRole(String $roleName)
    {
        $this->roles()->toggle($roleName);
        return $this->touch();
    }

    /**
     * Set role to user.
     */
    public function setRole(String $roleName)
    {
        $actualRoles = $this->roles->pluck('name_id');
        if (!$actualRoles->contains($roleName)) {
            $actualRoles->push($roleName);
        }
        $this->roles()->sync($actualRoles);
        $this->touch();
        return $this;
    }


    /**
     * @param $roles
     * @return User
     */
    public function syncRoles($roles)
    {
        $this->roles()->sync($roles);
        $this->touch();
        return $this;
    }

    /**
     * Check if user Have Role Assigned.
     */
    public function hasRole(String $roleName)
    {
        return $this->roles->pluck('name_id')->contains($roleName);
    }

    /**
     * @return HasMany
     */
    public function accountsOld()
    {
        return $this->hasMany(Account::class);
    }
    /**
     * @return HasMany
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
        return $this->belongsToMany(User::class, 'users_receivers', 'user_id', 'receiver_id')->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function senders()
    {
        return $this->belongsToMany(User::class, 'users_receivers', 'receiver_id', 'user_id')->withTimestamps();
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return array
     */
    public function getDataFromUserForToken()
    {
        return [
            'idn' => $this->idn,
            'idn_type' => $this->idn_type,
        ];
    }
}
