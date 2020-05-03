<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
