<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Land extends Model
{
    use CrudTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nft',
        'owner_id',
        'type',
        'size',
        'image',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'size' => 'integer',
        'image' => 'string',
    ];

    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        $disk = "public";
        $destination_path = "uploads/images/lands";
        $destination_path = "lands";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path, $fileName = null);

        // return $this->attributes[{$attribute_name}]; // uncomment if this is a translatable field
    }



//    public function user(): BelongsTo
//    {
//        return $this->belongsTo(User::class);
//    }
}
