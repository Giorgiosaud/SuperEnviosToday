<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;
  use Illuminate\Database\Eloquent\Relations\BelongsToMany;
  use Illuminate\Database\Eloquent\Relations\HasMany;
  use Illuminate\Database\Eloquent\SoftDeletes;
  use Illuminate\Support\Facades\DB;

  /**
   * @method static create(array $data)
   * @method static find($venezuelan_operator_account_id)
   * @method static where(string $string, $bank_id)
   * @method static whereIsOperator(bool $true)
   * @method static select(string $string)
   * @property mixed balanceCache
   * @property mixed id
   * @property mixed bank
   */
  class Account extends Model
  {
    use HasFactory;
    use SoftDeletes;

    //protected $with = ['bank'];
    protected $fillable = ['bank_id', 'type', 'number', 'is_operator'];

    /**
     * @return BelongsTo
     */
    public function bank()
    {
      return $this->belongsTo(Bank::class);
    }

    /**
     * @return Account|HasMany
     */
    public function transactions()
    {
      return $this->hasMany(Transaction::class);
    }

    /**
     * @return BelongsToMany
     */
    public function owners()
    {
      return $this->belongsToMany(User::class);
    }

    /**
     * @return float|int
     */
    public function getBalanceAttribute()
    {
      $sum = DB::selectOne("SELECT SUM(amount) AS amount FROM transactions WHERE account_id=? GROUP BY `account_id`", [$this->id]);
      if ($sum) {
        return $sum->amount / 10000;
      }
      return 0;
    }
  }
