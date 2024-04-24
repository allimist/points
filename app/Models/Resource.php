<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resource extends Model
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
        'size',
        'type',
        'skill_id',
        'image',
        'image_hover',
        'health',
        'reload',
        'revenue',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'size' => 'integer',
        'skill_id' => 'integer',
        'health' => 'integer',
        'reload' => 'integer',
        'revenue' => 'array',
//    'image'
    ];

    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        $disk = "public";
        $destination_path = "resources";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path, $fileName = null);

        // return $this->attributes[{$attribute_name}]; // uncomment if this is a translatable field
    }


    public function setImageHoverAttribute($value)
    {
        $attribute_name = "image_hover";
        $disk = "public";
        $destination_path = "resources";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path, $fileName = null);

        // return $this->attributes[{$attribute_name}]; // uncomment if this is a translatable field
    }


//    public function currencysecond(): BelongsTo
//    {
//        return $this->belongsTo(Currency::class);
//    }
//
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
