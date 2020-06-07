<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1)
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    //
}
