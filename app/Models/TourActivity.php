<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TourActivity extends Model
{
    use HasTranslations;

    protected $table = 'tour_activities';

    public $translatable = [
        'title',
        'description',
    ];

    protected $fillable = [
        'tour_itinerary_id',
        'activity_type',
        'start_time',
        'end_time',
        'title',
        'description',
        'image_url',
    ];

    public function tour_itinerary()
    {
        return $this->belongsTo(TourItinerary::class, 'tour_itinerary_id');
    }
}
