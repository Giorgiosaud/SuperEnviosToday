<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;

  /**
   * @method static create(array $array)
   * @method static find(string $string)
   */
  class Role extends Model
  {
    use HasFactory;

    protected $fillable = ['name_id', 'name'];
    protected $primaryKey = 'name_id';
    public $incrementing = false;

    /**
     * The roles that belong to the user.
     */
    public function users()
    {
      return $this->belongsToMany(User::class)->withTimestamps();
    }
    //
  }
