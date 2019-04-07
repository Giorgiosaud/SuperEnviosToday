<?php

namespace App;

use App\Scopes\RateOrderScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Rate
 *
 * @package App
 * @method static Rate whereCurrencyId($foreign_currency_id)
 * @method static Rate orderBy(string $string, string $string1)
 * @method first()
 * @property int $id
 * @property int $currency_id
 * @property mixed $since
 * @property int $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Rate newModelQuery()
 * @method static Builder|Rate newQuery()
 * @method static Builder|Rate query()
 * @method static Builder|Rate whereAmount($value)
 * @method static Builder|Rate whereCreatedAt($value)
 * @method static Builder|Rate whereId($value)
 * @method static Builder|Rate whereSince($value)
 * @method static Builder|Rate whereUpdatedAt($value)
 * @mixin \Eloquent
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
