<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TourImage
 * 
 * @property int $id
 * @property int $tour_id
 * @property string $image_url
 * @property bool|null $is_primary
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Tour $tour
 *
 * @package App\Models
 */
class TourImage extends Model
{
	protected $table = 'tour_images';

	protected $casts = [
		'tour_id' => 'int',
		'is_primary' => 'bool'
	];

	protected $fillable = [
		'tour_id',
		'image_url',
		'is_primary'
	];

	public function tour()
	{
		return $this->belongsTo(Tour::class);
	}
}
