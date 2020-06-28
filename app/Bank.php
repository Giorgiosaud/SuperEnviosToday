<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static select(string $string)
 */
class Bank extends Model
{
    protected $perPage=50;
    use SoftDeletes;

    protected $fillable = ['currency_id', 'name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('currency_id', 'desc');
        });
    }

    /**
     * @return BelongsTo
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
