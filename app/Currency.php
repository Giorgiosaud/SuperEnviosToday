<?php

namespace App;

use App\Observers\CurrencyObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Currency.
 *
 * @method static Currency whereName(string $string)
 * @method static Currency first()
 *
 * @property int $id
 * @property string $name
 * @property string $identificator
 * @property string $sign
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Bank[] $banks
 *
 * @method static Builder|Currency newModelQuery()
 * @method static Builder|Currency newQuery()
 * @method static Builder|Currency query()
 * @method static Builder|Currency whereCreatedAt($value)
 * @method static Builder|Currency whereId($value)
 * @method static Builder|Currency whereIdentificator($value)
 * @method static Builder|Currency whereSign($value)
 * @method static Builder|Currency whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Currency extends Model
{
    protected $fillable = ['name', 'identificator', 'sign'];

    public static function boot()
    {
        parent::boot();
        self::observe(new CurrencyObserver());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function banks()
    {
        return $this->hasMany(Bank::class);
    }

    //
}
