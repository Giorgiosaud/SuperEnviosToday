<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Transaction
 *
 * @method static create($validData)
 * @property int $id
 * @property int $amount
 * @property int|null $from_account_id
 * @property int|null $emitter_operator
 * @property int $to_account_id
 * @property int|null $from_client_id
 * @property int|null $foreign_currency_id
 * @property int|null $transaction_related
 * @property string $status
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account $destinationAccount
 * @property-read Account|null $originAccount
 * @method static Builder|Transaction newModelQuery()
 * @method static Builder|Transaction newQuery()
 * @method static Builder|Transaction query()
 * @method static Builder|Transaction whereAmount($value)
 * @method static Builder|Transaction whereCreatedAt($value)
 * @method static Builder|Transaction whereEmitterOperator($value)
 * @method static Builder|Transaction whereForeignCurrencyId($value)
 * @method static Builder|Transaction whereFromAccountId($value)
 * @method static Builder|Transaction whereFromClientId($value)
 * @method static Builder|Transaction whereId($value)
 * @method static Builder|Transaction whereStatus($value)
 * @method static Builder|Transaction whereToAccountId($value)
 * @method static Builder|Transaction whereTransactionRelated($value)
 * @method static Builder|Transaction whereType($value)
 * @method static Builder|Transaction whereUpdatedAt($value)
 */
class Transaction extends Model
{
    protected $fillable=['type','to_account_id','from_account_id','amount','from_client_id','to_receiver_id','emitter_operator','foreign_currency_id','status','emitter_operator'];
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
