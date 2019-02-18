<?php

namespace App;

use App\Scopes\RateOrderScope;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Rate
 * @package App
 */
class Rate extends Model
{
    protected $casts = [
        'since' => 'datetime:Y-m-d h:i:s',
    ];
    protected $fillable=['currency_id' ,'amount','since' ];
    /**
     *
     */
    public function getAmountAttribute($value)
    {
        return $value/10000;
    }
    public function setAmountAttribute($value)
    {
        $this->attributes['amount']= $value*10000;
    }
    protected static function boot() {
        parent::boot();
        static::addGlobalScope(new RateOrderScope('since', 'DESC'));
    }
    //
}
