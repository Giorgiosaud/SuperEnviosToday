<?php

  namespace App\Models;

  use App\Scopes\RateOrderScope;
  use Carbon\Carbon;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;
  use Illuminate\Support\Collection;

  /**
   * @method static Rate whereCurrencyId($currencyId)
   * @method static Rate orderBy(string $string, string $string1)
   * @method static Collection first()
   * @method where(string $string, string $string1, Carbon $now)
   * @method static paginate()
   */
  class Rate extends Model
  {
    use HasFactory;

    protected $perPage = 300;

    /**
     *
     */
    protected static function boot()
    {
      parent::boot();
      static::addGlobalScope(new RateOrderScope('since', 'DESC'));
    }

    protected $casts = [
      'since' => 'datetime',
      'amount' => 'integer'
    ];
    protected $fillable = ['currency_id', 'amount', 'since', 'message'];

    public function getAmountAttribute($value)
    {
      return $value / (10000*1000000);
    }

    public function setAmountAttribute($value)
    {
      $this->attributes['amount'] = strval($value * (10000*1000000));
    }

    /**
     * @return BelongsTo
     */
    public function currency()
    {
      return $this->belongsTo(Currency::class);
    }
    //
  }
