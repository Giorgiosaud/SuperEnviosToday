<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;
  use Illuminate\Database\Eloquent\Relations\HasMany;
  use Illuminate\Database\Eloquent\SoftDeletes;

  /**
   * @method static select(string $string)
   * @method static whereIn(string $string, false|string[] $currencies)
   */
  class Bank extends Model
  {
    use HasFactory;

    /**
     * @var int
     */
    protected $perPage = 50;

    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = ['currency_id', 'name'];

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
     * @return BelongsTo
     */
    public function currency()
    {
      return $this->belongsTo(Currency::class);
    }

    /**
     * @return HasMany
     */
    public function accounts()
    {
      return $this->hasMany(Account::class);
    }
    //
  }
