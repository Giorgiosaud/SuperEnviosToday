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

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
 

    public function getAmountAttribute($value)
    {
        return $value / (10000*1000000);
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = strval($value * (10000*1000000));
    }

    public function attachments()
    {
        return  $this->morphToMany(Attachment::class, 'attachable');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function operator(){
        return $this->belongsTo(User::class, 'operator_id');
    }
    public function related(){
        return $this->hasMany(Transaction::class, 'track_number','track_number');
    }
    public function venezuelanRelated(){
      return $this->hasMany(Transaction::class, 'track_number','track_number')
      ->where('amount','>',0)
      ->with('account.owners')
      ->whereHas('account.bank',function($q){
        $q->where('currency_id','2');
      });
    }
    public function foreignRelated(){
      return $this->hasMany(Transaction::class, 'track_number','track_number')
      ->where('amount','>',0)
      ->with('client')
      ->whereHas('account.bank',function($q){
        $q->where('currency_id','!=','2');
      });
    }
}
