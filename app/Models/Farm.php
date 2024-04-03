<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Farm extends Model
{
    use CrudTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'resource_id',
        'land_id',
        'size',
        'posx',
        'posy',
        'is_public',

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'resource_id' => 'integer',
        'land_id' => 'integer',
        'size' => 'integer',
        'posx' => 'integer',
        'posy' => 'integer',
        'is_public' => 'boolean',

    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }
}
