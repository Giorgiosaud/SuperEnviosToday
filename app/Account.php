<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static create(array $data)
 */
class Account extends Model
{
    //protected $with = ['bank'];
    protected $fillable = ['bank_id', 'is_operator_account', 'number', 'type'];

    /**
     * @return BelongsTo
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * @return \App\Account
     */
    public function owners()
    {
        return $this->belongsToMany(User::class);
    }
    /**
     * @return HasMany
     */
    public function incomingTransactions()
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    /**
     * @return HasMany
     */
    public function outgoingTransactions()
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    /**
     * @return HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, ['from_account_id','to_account_id']);
    }

    /**
     * @return HasMany
     */
    public function outgoingTransactionsTyped()
    {
        return $this->outgoingTransactions()->where('type', 'outcome');
    }

    public function incomingTransactionsTyped()
    {
        return $this->incomingTransactions()->where('type', 'income');
    }

    /**
     * @return mixed
     */
    public function getBalanceAttribute()
    {
        return $this->incomingTransactionsTyped->sum('amount') - $this->outgoingTransactionsTyped->sum('amount');
    }
}
