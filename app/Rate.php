<?php

namespace App;

use App\Scopes\RateOrderScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @method static Rate whereCurrencyId($currencyId)
 * @method static Rate orderBy(string $string, string $string1)
 * @method static Collection first()
 * @method where(string $string, string $string1, \Carbon\Carbon $now)
 */
class Rate extends Model
{
    protected $casts = [
        'since' => 'datetime',
        'amount'=>'integer'
    ];
    protected $fillable = ['currency_id', 'amount', 'since'];

    public function getAmountAttribute($value)
    {
        return $value / 10000;
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = strval($value * 10000);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new RateOrderScope('since', 'DESC'));
    }
    //
}
