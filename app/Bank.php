<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Storage;

    class Bank extends Model
    {
        protected $fillable = ['currency_id', 'name'];
        protected $with=['currency'];
        public function currency(){
            return $this->belongsTo(Currency::class);
        }
        public function accounts()
        {
            return $this->hasMAny(Account::class);
        }
    }
