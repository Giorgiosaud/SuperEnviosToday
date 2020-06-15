<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @method static create(array $data)
 * @method static find($venezuelan_operator_account_id)
 * @method static where(string $string, $bank_id)
 */
class Account extends Model
{
    use SoftDeletes;

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
     * @return BelongsToMany
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

    public function balanceCache(){
      return $this->hasOne(BalanceCache::class);
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
    public function outgoingTransactionsTyped($lastTransactionId)
    {
      if($lastTransactionId){
        return $this->outgoingTransactions()->where('id','>',$lastTransactionId)->where('type', 'outcome');
      }
      return $this->outgoingTransactions()->where('type', 'outcome');
    }

    public function incomingTransactionsTyped($lastTransactionId)
    {
      if($lastTransactionId){
        return $this->incomingTransactions()->where('id','>',$lastTransactionId)->where('type', 'income');
      }
      return $this->incomingTransactions()->where('type', 'income');
    }

    /**
     * @return mixed
     * TODO: refactor this to work with multiples caches and make it work on past balance
     */
    public function getBalanceAttribute()
    {
      $cachedBalanceTransactionId= $this->balanceCache?$this->balanceCache->transaction_id:null;

      $incomingTransactionsNoCached=$this->incomingTransactionsTyped($cachedBalanceTransactionId)->get();

      $outgoingTransactionsNoCached=$this->outgoingTransactionsTyped($cachedBalanceTransactionId)->get();
      $sumIncomings=$incomingTransactionsNoCached->sum('amount');
      $sumOutgoings=$outgoingTransactionsNoCached->sum('amount');
      $cache_balance_diff_amount = $sumIncomings - $sumOutgoings;
      $cachedBalanceAmount= $cachedBalanceTransactionId?$this->balanceCache->amount:0;
      $newCachedBalance=$cachedBalanceAmount+$cache_balance_diff_amount;
      if($incomingTransactionsNoCached->count()+$outgoingTransactionsNoCached->count()>15){
        $maxId=max($incomingTransactionsNoCached->last()->id,$outgoingTransactionsNoCached->last()->id);
        if($cachedBalanceTransactionId){
          $this->balanceCache->transaction_id=$maxId;

          $this->balanceCache->amount=strval($newCachedBalance);
          $this->save();
        }else{
          $this->balanceCache()->create(['transaction_id'=>$maxId,'amount'=>strval($newCachedBalance)]);
        }
        return $newCachedBalance;
      }
        return $newCachedBalance;
    }
}
