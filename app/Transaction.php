<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable=['to_account_id','form_account_id','amount'];
    public function originAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function destinationAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }
    public function getAmountAttribute($value)
    {
        return $value/10000;
    }
    public function setAmountAttribute($value)
    {
        $this->attributes['amount']= $value*10000;
    }
}
