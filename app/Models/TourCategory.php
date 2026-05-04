<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TourCategory
 * 
 * @property int $tour_id
 * @property int $category_id
 * 
 * @property Tour $tour
 * @property Category $category
 *
 * @package App\Models
 */
class TourCategory extends Model
{
	protected $table = 'tour_categories';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'tour_id' => 'int',
		'category_id' => 'int'
	];

	public function tour()
	{
		return $this->belongsTo(Tour::class);
	}

	public function category()
	{
		return $this->belongsTo(Category::class);
	}
}
