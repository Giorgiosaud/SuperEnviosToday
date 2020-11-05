<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\HasMany;
  use Illuminate\Database\Eloquent\SoftDeletes;

  /**
   * @property Bank banks
   * @method static Currency create(array $data)
   * @method static Builder whereId($currency)
   * @method static where(string $string, $currency)
   */
  class Currency extends Model
  {

    use SoftDeletes, HasFactory;

    protected $fillable = ['name', 'identifier', 'sign', 'separator', 'decimal' ,'precision','symbol'];

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
    public function accounts(){
        return $this->hasManyThrough(Account::class, Bank::class);

    }
    //
  }
