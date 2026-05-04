<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TourGuide
 * 
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property string|null $bio
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|ScheduleGuide[] $schedule_guides
 *
 * @package App\Models
 */
class TourGuide extends Model
{
	protected $table = 'tour_guides';

	protected $fillable = [
		'name',
		'phone',
		'email',
		'bio'
	];

	public function schedule_guides()
	{
		return $this->hasMany(ScheduleGuide::class, 'guide_id');
	}
}
