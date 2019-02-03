<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function originAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }
    public function destinationAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');

    }
}
