<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BalanceCache extends Model
{
  protected $table='balance_caches';
  protected $fillable = ['transaction_id', 'amount']; // this allows the create method to work
  public function getAmountAttribute($value)
  {
      return $value / 10000;
  }

  public function setAmountAttribute($value)
  {
      $this->attributes['amount'] = strval($value * 10000);
  }
    //
}
