<?php

    namespace App;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Carbon;

    /**
     * App\Account
     *
     * @method static create($validInputs)
     * @property int $id
     * @property int $bank_id
     * @property int $user_id
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
     * @method static Builder|Account whereUserId($value)
     * @mixin \Eloquent
     */
    class Account extends Model
    {
        protected $with = ['bank'];
        protected $fillable = ['bank_id', 'user_id', 'is_operator_account', 'number'];
        protected $appends = ['TotalAmount'];

        /**
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function transactions()
        {
            return $this->hasMany(Transaction::class, 'to_account_id');
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function outgoingTransactions()
        {
            return $this->transactions()->where('type', 'outcome');
        }

        public function incomingTransactions()
        {
            return $this->transactions()->where('type', 'income');
        }

        /**
         * @return mixed
         */
        public function getBalanceAttribute()
        {
            return ($this->incomingTransactions->sum('amount') - $this->outgoingTransactions->sum('amount') )/ 10000;
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function owner()
        {
            return $this->belongsTo(User::class, 'user_id');
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function bank()
        {
            return $this->belongsTo(Bank::class);
        }

    }
