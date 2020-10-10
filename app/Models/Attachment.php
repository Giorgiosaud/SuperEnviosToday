<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static find($attachmentId)
 * @method static whereIn(array $attachmentsId)
 */
class Attachment extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'path',
        'extension',
    ];
    public function transactions(){
      return $this->morphedByMany(Transaction::class,'attachable');
    }
  public function pendingTransactions(){
    return  $this->morphedByMany(Transaction::class,'attachable');
  }
    //
}
