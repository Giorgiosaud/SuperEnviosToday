<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
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
