<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Accommodation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'destination_id',
        'name',
        'address',
        'description',
        'star_rating',
        'is_active',
    ];

    protected $casts = [
        'star_rating' => 'integer',
        'is_active' => 'boolean',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function room_types()
    {
        return $this->hasMany(RoomType::class);
    }
}
