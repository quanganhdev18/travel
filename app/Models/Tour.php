<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
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
        'departure_time',
        'meeting_point',
        'title',
        'slug',
        'description',
        'duration_days',
        'duration_nights',
        'base_price',
        'child_price',
        'ai_tags',
        'departure_province_id',
        'departure_ward_id',
        'destination_province_id',
        'destination_ward_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($tour) {
            if ($tour->isDirty('destination_ward_id') || ($tour->destination_ward_id && is_null($tour->destination_id))) {
                $tour->destination_id = $tour->resolveDestinationId();
            }
            if ($tour->isDirty('departure_ward_id') || ($tour->departure_ward_id && is_null($tour->departure_location_id))) {
                $tour->departure_location_id = $tour->resolveDepartureLocationId();
            }
        });
    }

    public function resolveDestinationId(): ?int
    {
        if (! $this->destination_ward_id) {
            return null;
        }

        $ward = Ward::find($this->destination_ward_id);
        if (! $ward) {
            return null;
        }

        $path = ($ward->path_with_type ?? '').' '.($ward->path ?? '');
        $normalizedPath = str_replace(' ', '', mb_strtolower($path, 'UTF-8'));

        $destinations = Destination::all();
        foreach ($destinations as $destination) {
            $translations = is_array($destination->getTranslations('name'))
                ? $destination->getTranslations('name')
                : [$destination->name];

            foreach ($translations as $name) {
                if (empty($name)) {
                    continue;
                }
                $normalizedName = str_replace(' ', '', mb_strtolower($name, 'UTF-8'));
                if (str_contains($normalizedPath, $normalizedName)) {
                    return $destination->id;
                }
            }
        }

        return null;
    }

    public function resolveDepartureLocationId(): ?int
    {
        if (! $this->departure_ward_id) {
            return null;
        }

        $ward = Ward::find($this->departure_ward_id);
        if (! $ward) {
            return null;
        }

        $path = ($ward->path_with_type ?? '').' '.($ward->path ?? '');
        $normalizedPath = str_replace(' ', '', mb_strtolower($path, 'UTF-8'));

        $destinations = Destination::all();
        foreach ($destinations as $destination) {
            $translations = is_array($destination->getTranslations('name'))
                ? $destination->getTranslations('name')
                : [$destination->name];

            foreach ($translations as $name) {
                if (empty($name)) {
                    continue;
                }
                $normalizedName = str_replace(' ', '', mb_strtolower($name, 'UTF-8'));
                if (str_contains($normalizedPath, $normalizedName)) {
                    return $destination->id;
                }
            }
        }

        return null;
    }

    public function getDestinationAttribute()
    {
        if ($this->relationLoaded('destination')) {
            $relation = $this->getRelation('destination');
            if ($relation !== null) {
                return $relation;
            }
        } else {
            $relation = $this->belongsTo(Destination::class, 'destination_id')->getResults();
            if ($relation !== null) {
                return $relation;
            }
        }

        if ($this->destination_province_id) {
            $province = $this->destination_province;
            if ($province) {
                $mock = new Destination;
                $mock->id = 0;
                $mock->setTranslation('name', 'vi', $province->name);
                $mock->setTranslation('name', 'en', $province->name);
                $mock->setTranslation('name', 'zh', $province->name);

                return $mock;
            }
        }

        return null;
    }

    public function getDepartureLocationAttribute()
    {
        if ($this->relationLoaded('departure_location')) {
            $relation = $this->getRelation('departure_location');
            if ($relation !== null) {
                return $relation;
            }
        } else {
            $relation = $this->belongsTo(Destination::class, 'departure_location_id')->getResults();
            if ($relation !== null) {
                return $relation;
            }
        }

        if ($this->departure_province_id) {
            $province = $this->departure_province;
            if ($province) {
                $mock = new Destination;
                $mock->id = 0;
                $mock->setTranslation('name', 'vi', $province->name);
                $mock->setTranslation('name', 'en', $province->name);
                $mock->setTranslation('name', 'zh', $province->name);

                return $mock;
            }
        }

        return null;
    }

    public function departure_location()
    {
        return $this->belongsTo(Destination::class, 'departure_location_id');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function departure_province()
    {
        return $this->belongsTo(Province::class, 'departure_province_id');
    }

    public function departure_ward()
    {
        return $this->belongsTo(Ward::class, 'departure_ward_id');
    }

    public function destination_province()
    {
        return $this->belongsTo(Province::class, 'destination_province_id');
    }

    public function destination_ward()
    {
        return $this->belongsTo(Ward::class, 'destination_ward_id');
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
            ->whereRaw("TIMESTAMP(DATE(departure_date), COALESCE((select departure_time from tours where tours.id = tour_schedules.tour_id), '00:00:00')) >= ?", [Carbon::now()->addDays(3)->toDateTimeString()])
            ->where('status', 'available')
            ->orderBy('departure_date', 'asc');
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'tour_tickets', 'tour_id', 'ticket_id');
    }

    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'tour_addons', 'tour_id', 'addon_id')
            ->withTimestamps();
    }
}
