<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
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
        'resource_id',
        'skill_id',
        'level',
        'cost',
        'revenue',
        'xp',
        'time',
        'reload',
        'damage',
        'image_init',
        'image_ready',
        'image_reload',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'resource_id' => 'integer',
        'skill_id' => 'integer',
        'level' => 'integer',
        'cost' => 'array',
        'revenue' => 'array',
        'xp' => 'integer',
        'time' => 'integer',
        'reload' => 'integer',
        'damage' => 'integer',
        'image_init' => 'string',
        'image_ready' => 'string',
        'image_reload' => 'string',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

//    public function currency(): BelongsTo
//    {
//        return $this->belongsTo(Currency::class);
//    }

    public function setImageInitAttribute($value)
    {
        $attribute_name = "image_init";
        $disk = "public";
        $destination_path = "services";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path, $fileName = null);
    }

    public function setImageReadyAttribute($value)
    {
        $attribute_name = "image_ready";
        $disk = "public";
        $destination_path = "services";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path, $fileName = null);
    }

    public function setImageReloadAttribute($value)
    {
        $attribute_name = "image_reload";
        $disk = "public";
        $destination_path = "services";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path, $fileName = null);
    }


}
