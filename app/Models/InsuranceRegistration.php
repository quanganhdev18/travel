<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceRegistration extends Model
{
    protected $fillable = [
        'registration_code',
        'user_id',
        'fullname',
        'phone',
        'email',
        'package_code',
        'package_name',
        'price_per_day',
        'total_days',
        'total_price',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'price_per_day' => 'decimal:2',
            'total_price' => 'decimal:2',
            'total_days' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
