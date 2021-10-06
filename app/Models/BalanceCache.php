<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceCache extends Model
{
  use HasFactory;
  protected $fillable = ['transaction_id', 'amount']; // this allows the create method to work
  public function getAmountAttribute($value)
  {
      return $value / (10000*1000000);
  }

  public function setAmountAttribute($value)
  {
      $this->attributes['amount'] = strval($value * (10000*1000000));
  }
    //
}
