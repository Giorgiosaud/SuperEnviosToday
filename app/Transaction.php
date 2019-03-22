<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable=['type','to_account_id','from_account_id','amount','from_client_id','to_receiver_id','emitter_operator','foreign_currency_id','status'];
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
