<?php

namespace App;

use App\Observers\CurrencyObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Bank banks
 * @method static create(array $data)
 * @method static where(string $string, $name)
 */
class Currency extends Model
{
    use SoftDeletes;
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
