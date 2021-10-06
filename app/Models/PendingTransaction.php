<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;

  /**
   * @method static create(array $ptData)
   * @method static where(string $string, string $string1)
   * @property mixed attachments
   */
  class PendingTransaction extends Model
  {
    use HasFactory;

    protected $fillable = [
      'client_id',
      'operator_id',
      'receiver_id',
      'venezuelan_operator_id',
      'operator_account_id',
      'received_transaction_attachment_id',
      'receiver_account_id',
      'venezuelan_operator_account_id',
      'rate',
      'amount',
      'transaction_number',
      'status',
    ];

    protected $casts = ['amount' => 'integer', 'rate' => 'integer'];

    public function attachments()
    {
      return $this->morphToMany(Attachment::class, 'attachable');
    }

    public function client()
    {
      return $this->belongsTo(User::class, 'client_id');
    }

    public function receiver()
    {
      return $this->belongsTo(User::class, 'receiver_id');
    }

    public function venezuelanOperator()
    {
      return $this->belongsTo(User::class, 'venezuelan_operator_id');
    }

    public function foreignOperator()
    {
      return $this->belongsTo(User::class, 'operator_id');
    }

    public function foreignAccount()
    {
      return $this->belongsTo(Account::class, 'operator_account_id');
    }

    public function receiverAccount()
    {
      return $this->belongsTo(Account::class, 'receiver_account_id');
    }

    public function localOperatorAccount()
    {
      return $this->belongsTo(Account::class, 'venezuelan_operator_account_id');
    }

    public function getRateAttribute($value)
    {
      return $value / (10000*1000000);
    }

    public function setRateAttribute($value)
    {
      $this->attributes['rate'] = strval($value * (10000*1000000));
    }

    public function getAmountAttribute($value)
    {
      return $value / (10000*1000000);
    }

    public function setAmountAttribute($value)
    {
      $this->attributes['amount'] = strval($value * (10000*1000000));
    }
  }
