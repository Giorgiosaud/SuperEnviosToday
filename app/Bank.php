<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static select(string $string)
 */
class Bank extends Model
{
    protected $fillable = ['currency_id', 'name'];
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('currency_id', 'desc');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function accounts()
    {
        return $this->hasMAny(Account::class);
    }
    //
}
