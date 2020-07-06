<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1)
 */
class Setting extends Model
{
    /**
     * @var int
     */
    protected $perPage=50;

    /**
     * @var string[]
     */
    protected $fillable = ['key', 'value'];

    //
}
