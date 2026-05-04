<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Addon
 * 
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property string|null $image_url
 * @property bool|null $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|Booking[] $bookings
 *
 * @package App\Models
 */
class Addon extends Model
{
	protected $table = 'addons';

	protected $casts = [
		'price' => 'float',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'name',
		'description',
		'price',
		'image_url',
		'is_active'
	];

	public function bookings()
	{
		return $this->belongsToMany(Booking::class, 'booking_addons')
					->withPivot('id', 'addon_name', 'price', 'quantity')
					->withTimestamps();
	}
}
