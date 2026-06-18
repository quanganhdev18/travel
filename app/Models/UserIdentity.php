<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserIdentity
 *
 * @property int $id
 * @property int $user_id
 * @property string $identity_number
 * @property string $full_name
 * @property Carbon $date_of_birth
 * @property string|null $gender
 * @property string|null $nationality
 * @property string|null $place_of_origin
 * @property string|null $place_of_residence
 * @property Carbon $issue_date
 * @property Carbon $expiry_date
 * @property string $issue_place
 * @property string|null $front_image_url
 * @property string|null $back_image_url
 * @property string|null $verification_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 */
class UserIdentity extends Model
{
    protected $table = 'user_identities';

    protected $casts = [
        'user_id' => 'int',
        'date_of_birth' => 'datetime',
        'issue_date' => 'datetime',
        'expiry_date' => 'datetime',
        'front_image_url' => 'encrypted',
        'back_image_url' => 'encrypted',
    ];

    protected $fillable = [
        'user_id',
        'identity_number',
        'full_name',
        'date_of_birth',
        'gender',
        'nationality',
        'place_of_origin',
        'place_of_residence',
        'issue_date',
        'expiry_date',
        'issue_place',
        'front_image_url',
        'back_image_url',
        'verification_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
