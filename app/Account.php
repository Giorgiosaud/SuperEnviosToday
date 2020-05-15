<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
/**
 * App\Account.
 *
 * @method static create($validInputs)
 *
 * @property int $id
 * @property int $bank_id
 * @property string|null $type
 * @property string $number
 * @property int $is_operator_account
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bank $bank
 * @property-read mixed $total_amount
 * @property-read Collection|Transaction[] $incomingTransactions
 * @property-read Collection|Transaction[] $outgoingTransactions
 * @property-read User $owner
 *
 * @method static Builder|Account newModelQuery()
 * @method static Builder|Account newQuery()
 * @method static Builder|Account query()
 * @method static Builder|Account whereBankId($value)
 * @method static Builder|Account whereCreatedAt($value)
 * @method static Builder|Account whereId($value)
 * @method static Builder|Account whereIsOperatorAccount($value)
 * @method static Builder|Account whereNumber($value)
 * @method static Builder|Account whereType($value)
 * @method static Builder|Account whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property-read mixed $balance
 * @property-read Collection|Transaction[] $transactions
 */
class Account extends Model
{
    protected $with = ['bank'];
    protected $fillable = ['bank_id', 'is_operator_account', 'number', 'type'];
    //protected $appends = ['Balance'];

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

    /**
     * @return BelongsTo
     */
    public function owners()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
