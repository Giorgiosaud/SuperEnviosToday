<?php

namespace App;

use App\Observers\CurrencyObserver;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable=['name','identificator','sign'];
    public static function boot()
    {
        parent::boot();
        Currency::observe(new CurrencyObserver());
    }
    public function banks(){
        return $this->hasMany(Bank::class);
    }
    //
}
