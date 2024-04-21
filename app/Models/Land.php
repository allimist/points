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
        'type_id',
//        'size',
//        'image',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
//        'size' => 'integer',
        'user_id' => 'integer',
        'type_id' => 'integer',
//        'image' => 'string',
    ];

//    public function setImageAttribute($value)
//    {
//        $attribute_name = "image";
//        $disk = "public";
//        $destination_path = "uploads/images/lands";
//        $destination_path = "lands";
//
//        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path, $fileName = null);
//
//        // return $this->attributes[{$attribute_name}]; // uncomment if this is a translatable field
//    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(LandType::class);
    }



//    public function user(): BelongsTo
//    {
//        return $this->belongsTo(User::class);
//    }
}
