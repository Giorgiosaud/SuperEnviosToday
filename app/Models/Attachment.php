<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Attachment create(array $array)
 * @method static Attachment find($attachmentId)
 * @method static whereIn(string $string, array $attachmentsId)
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
