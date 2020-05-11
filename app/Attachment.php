<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class Attachment extends Model
{
    protected $fillable = [
        'name',
        'path',
        'extension',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }
    //
}
