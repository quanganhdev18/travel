<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * Class Tour
 */
class Tour extends Model
{
    use HasTranslations, SoftDeletes;

    protected $table = 'tours';

    protected $casts = [
        'destination_id' => 'int',
        'departure_location_id' => 'int',
        'duration_days' => 'int',
        'duration_nights' => 'int',
        'base_price' => 'float',
        'child_price' => 'float',
    ];

    public $translatable = [
        'title',
        'description',
    ];

    protected $fillable = [
        'destination_id',
        'departure_location_id',
        'title',
        'slug',
        'description',
        'duration_days',
        'duration_nights',
        'base_price',
        'child_price',
        'ai_tags',
    ];

    public function departure_location()
    {
        return $this->belongsTo(Destination::class, 'departure_location_id');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'tour_id');
    }

    public function tour_categories()
    {
        return $this->hasMany(TourCategory::class, 'tour_id');
    }

    public function tour_images()
    {
        return $this->hasMany(TourImage::class, 'tour_id');
    }

    public function tour_itineraries()
    {
        return $this->hasMany(TourItinerary::class, 'tour_id')
            ->orderBy('day_number', 'asc');
    }

    public function tour_schedules()
    {
        return $this->hasMany(TourSchedule::class, 'tour_id')
            ->orderBy('departure_date', 'asc');
    }

    public function tour_videos()
    {
        return $this->hasMany(TourVideo::class, 'tour_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'tour_id');
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'tour_categories',
            'tour_id',
            'category_id'
        );
    }

    public function identity()
    {
        return $this->hasOne(UserIdentity::class, 'tour_id');
    }

    public function primaryImage()
    {
        return $this->hasOne(TourImage::class, 'tour_id')
            ->where('is_primary', 1);
    }

    public function activeSchedules()
    {
        return $this->hasMany(TourSchedule::class, 'tour_id')
            ->where('departure_date', '>=', now())
            ->where('status', 'available')
            ->orderBy('departure_date', 'asc');
    }
}
