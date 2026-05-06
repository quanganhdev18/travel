<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Tour
 *
 * @property int $id
 * @property int $destination_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property int $duration_days
 * @property int $duration_nights
 * @property float $base_price
 * @property string|null $ai_tags
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Destination $destination
 * @property Collection|Review[] $reviews
 * @property Collection|TourCategory[] $tour_categories
 * @property Collection|TourImage[] $tour_images
 * @property Collection|TourItinerary[] $tour_itineraries
 * @property Collection|TourSchedule[] $tour_schedules
 * @property Collection|TourVideo[] $tour_videos
 * @property Collection|Wishlist[] $wishlists
 * @property Collection|Category[] $categories
 * @property UserIdentity|null $identity
 */
class Tour extends Model
{
    use SoftDeletes;

    protected $table = 'tours';

    protected $casts = [
        'destination_id' => 'int',
        'duration_days' => 'int',
        'duration_nights' => 'int',
        'base_price' => 'float',
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
        'ai_tags',
    ];

    public function departure_location()
    {
        return $this->belongsTo(Destination::class, 'departure_location_id');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tour_categories()
    {
        return $this->hasMany(TourCategory::class);
    }

    public function tour_images()
    {
        return $this->hasMany(TourImage::class);
    }

    public function tour_itineraries()
    {
        return $this->hasMany(TourItinerary::class);
    }

    public function tour_schedules()
    {
        return $this->hasMany(TourSchedule::class);
    }

    public function tour_videos()
    {
        return $this->hasMany(TourVideo::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'tour_categories', 'tour_id', 'category_id');
    }

    public function identity()
    {
        return $this->hasOne(UserIdentity::class);
    }
}
