<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Coupon
 *
 * @property int $id
 * @property string $code
 * @property string $discount_type
 * @property float $discount_value
 * @property float|null $min_order_value
 * @property float|null $max_discount
 * @property Carbon $valid_from
 * @property Carbon $valid_until
 * @property int|null $usage_limit
 * @property int|null $used_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection|Booking[] $bookings
 * @property Collection|TicketBooking[] $ticket_bookings
 */
class Coupon extends Model
{
    protected $table = 'coupons';

    protected $casts = [
        'discount_value' => 'float',
        'min_order_value' => 'float',
        'max_discount' => 'float',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'usage_limit' => 'int',
        'used_count' => 'int',
    ];

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_value',
        'max_discount',
        'valid_from',
        'valid_until',
        'usage_limit',
        'used_count',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function ticket_bookings()
    {
        return $this->hasMany(TicketBooking::class);
    }
}
