<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1)
 */
class Setting extends Model
{
  use HasFactory;
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
