<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'client_id',
        'from_user_id',
        'from_account_id',
        'to_user_id',
        'to_account_id',
        'related_transaction_id',
        'transaction_number',
        'amount',
        'status',
        'type',
    ];

    protected $casts=['amount'=>'integer'];

    protected $with = ['attachments'];

    public function originAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function destinationAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function getAmountAttribute($value)
    {
        return $value / 10000;
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = strval($value * 10000);
    }

    public function attachments()
    {
        return  $this->morphMany(Attachment::class, 'attachable');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function relatedTransactions()
    {
        return $this->hasMany(self::class, 'related_transaction_id');
    }

    public function parentTransaction()
    {
        return $this->belongsTo(self::class, 'related_transaction_id');
    }
}
