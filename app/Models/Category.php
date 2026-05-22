<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * Class Category
 * 
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|TourCategory[] $tour_categories
 *
 * @package App\Models
 */
class Category extends Model
{
	use HasTranslations;

	protected $table = 'categories';

	protected $fillable = [
		'name',
		'slug'
	];

	public $translatable = [
		'name'
	];

	public function tour_categories()
	{
		return $this->hasMany(TourCategory::class);
	}
}
