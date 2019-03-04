<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $with=['bank'];
    protected $fillable=['bank_id','user_id','is_operator_account','number'];
    protected $appends=['TotalAmount'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class,'to_account_id');
    }

    public function getTotalAmountAttribute(){
        return $this->transactions->sum('amount');
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    //
}
