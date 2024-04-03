<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceUse extends Model
{
    use CrudTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'farm_id',
        'service_id',
        'amount',
        'claimed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'farm_id' => 'integer',
        'service_id' => 'integer',
        'amount' => 'integer',
        'claimed_at' => 'datetime',
    ];

//    public function user(): BelongsTo
//    {
//        return $this->belongsTo(User::class);
//    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
