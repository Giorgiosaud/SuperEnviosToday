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
    protected $fillable=['currency_id' ,'amount_bs','since' ];
    /**
     *
     */
    protected static function boot() {
        parent::boot();
        static::addGlobalScope(new RateOrderScope('since', 'DESC'));
    }
    //
}
