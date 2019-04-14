<?php

    namespace App;

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Carbon;

    /**
     * App\Attachment
     *
     * @method static Builder|Attachment newModelQuery()
     * @method static Builder|Attachment newQuery()
     * @method static Builder|Attachment query()
     * @mixin Eloquent
     * @property int $id
     * @property string $file_name
     * @property string $path
     * @property string $file_extension
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static Builder|Attachment whereCreatedAt($value)
     * @method static Builder|Attachment whereFileExtension($value)
     * @method static Builder|Attachment whereFileName($value)
     * @method static Builder|Attachment whereId($value)
     * @method static Builder|Attachment wherePath($value)
     * @method static Builder|Attachment whereUpdatedAt($value)
     */
    class Attachment extends Model
    {
        protected $fillable = [
            'name',
            'path',
            'extension'
        ];

        //
    }
