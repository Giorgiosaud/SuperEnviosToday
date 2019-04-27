<?php

    namespace App;

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Model;

    /**
     * App\PendingTransaction
     *
     * @method static Builder|PendingTransaction newModelQuery()
     * @method static Builder|PendingTransaction newQuery()
     * @method static Builder|PendingTransaction query()
     * @method static create(Collection $validData)
     * @mixin Eloquent
     */
    class PendingTransaction extends Model
    {
        protected $fillable = [
            'client_id',
            'foreign_account_id',
            'received_transaction_attachment_id',
            'receiver_account_id',
            'venezuelan_operator_account_id',
            'rate',
            'amount',
            'status'
        ];

        public function attachments()
        {
            return $this->morphMany(Attachment::class, 'attachable');
        }

        public function client()
        {
            return $this->belongsTo(User::class, 'client_id');
        }
        public function foreign_account(){
            return $this->belongsTo(Account::class, 'foreign_account_id');
        }
        public function receiver_account()
        {
            return $this->belongsTo(Account::class, 'receiver_account_id');
        }
        public function operator_account()
        {
            return $this->belongsTo(Account::class, 'venezuelan_operator_account_id');
        }

        public function getRateAttribute($value)
        {
            return $value / 10000;
        }

        public function setRateAttribute($value)
        {
            $this->attributes['rate'] = $value * 10000;
        }
        //
    }
