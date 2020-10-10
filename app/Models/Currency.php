<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\HasMany;
  use Illuminate\Database\Eloquent\SoftDeletes;

  /**
   * @property Bank banks
   * @method static create(array $data)
   * @method static whereId($currency)
   * @method static where(string $columnName, string $operator,string $value):Currency
   */
  class Currency extends Model
  {

    use SoftDeletes, HasFactory;

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
    public function accounts(){
        return $this->hasManyThrough(Account::class, Bank::class);

    }
    //
  }
