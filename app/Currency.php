<?php

namespace App;

use App\Observers\CurrencyObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $fillable = ['name', 'identifier', 'sign'];

    public static function boot()
    {
        parent::boot();
    }

    /**
     * @return HasMany
     */
    public function banks()
    {
        return $this->hasMany(Bank::class);
    }
    //
}
