<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Booking
 * 
 * @property int $id
 * @property int $user_id
 * @property int $tour_schedule_id
 * @property int|null $coupon_id
 * @property float $total_price
 * @property float|null $discount_amount
 * @property int $adults_count
 * @property int $children_count
 * @property string|null $booking_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property User $user
 * @property TourSchedule $tour_schedule
 * @property Coupon|null $coupon
 * @property Collection|Addon[] $addons
 * @property Collection|BookingPassenger[] $booking_passengers
 * @property Collection|Invoice[] $invoices
 * @property Collection|Payment[] $payments
 * @property Collection|Refund[] $refunds
 *
 * @package App\Models
 */
class Booking extends Model
{
	protected $table = 'bookings';

	protected $casts = [
		'user_id' => 'int',
		'tour_schedule_id' => 'int',
		'coupon_id' => 'int',
		'total_price' => 'float',
		'discount_amount' => 'float',
		'adults_count' => 'int',
		'children_count' => 'int'
	];

	protected $fillable = [
		'user_id',
		'tour_schedule_id',
		'coupon_id',
		'total_price',
		'discount_amount',
		'adults_count',
		'children_count',
		'booking_status'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function tour_schedule()
	{
		return $this->belongsTo(TourSchedule::class);
	}

	public function coupon()
	{
		return $this->belongsTo(Coupon::class);
	}

	public function addons()
	{
		return $this->belongsToMany(Addon::class, 'booking_addons')
					->withPivot('id', 'addon_name', 'price', 'quantity')
					->withTimestamps();
	}

	public function booking_passengers()
	{
		return $this->hasMany(BookingPassenger::class);
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class);
	}

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}

	public function refunds()
	{
		return $this->hasMany(Refund::class);
	}
}
