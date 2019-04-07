<?php

    namespace App;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Carbon;

    /**
 * App\Bank
 *
 * @method static Bank create($validated)
 * @method static Bank whereName(string $string)
 * @method Bank first()
 * @property int $id
 * @property int $currency_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Account[] $accounts
 * @property-read Currency $currency
 * @method static Builder|Bank newModelQuery()
 * @method static Builder|Bank newQuery()
 * @method static Builder|Bank query()
 * @method static Builder|Bank whereCreatedAt($value)
 * @method static Builder|Bank whereCurrencyId($value)
 * @method static Builder|Bank whereId($value)
 * @method static Builder|Bank whereUpdatedAt($value)
 * @mixin \Eloquent
 */
    class Bank extends Model
    {
        protected $fillable = ['currency_id', 'name'];
        protected $with = ['currency'];


        /**
         *
         */
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
    }
