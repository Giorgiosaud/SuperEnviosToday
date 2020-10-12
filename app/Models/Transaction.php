<?php

namespace App\Models;

use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static Builder with(array $assignedTransactionData)
 * @method static Builder whereIn(string $string, $accountsId)
 * @method static Builder where(string $string, $transactionNumber)
 * @method static Transaction create(array $data)
 */
class Transaction extends Model
{

    use SoftDeletes, HasFactory;
    protected $fillable = [
        //'from_user_id',
        //'from_account_id',
        //'to_user_id',
        //'to_account_id',
        //'related_transaction_id',
        //'transaction_number',
        //'type',
        'account_id',
        'client_id',
        'operator_id',
        'track_number',
        'bank_reference',
        'amount',
        'status',
        'comment'
    ];

    protected $casts=['amount'=>'integer'];

    //protected $with = ['attachments'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }
    /*TODO Delete*/
    public function originAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }
    /*TODO Delete*/

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    /*TODO Delete*/

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
        return  $this->morphToMany(Attachment::class, 'attachable');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    /*TODO Delete*/

    public function relatedTransactions()
    {
        return $this->hasMany(self::class, 'related_transaction_id');
    }
    /*TODO Delete*/

    public function parentTransaction()
    {
        return $this->belongsTo(self::class, 'related_transaction_id');
    }
    public function operator(){
        return $this->belongsTo(User::class, 'operator_id');
    }
    public function related(){
        return $this->hasMany(Transaction::class, 'track_number','track_number');
    }
}
