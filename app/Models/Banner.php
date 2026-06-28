<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Concerns\HasLocalImageUrl;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Banner
 *
 * @property int $id
 * @property string $title
 * @property string $image_url
 * @property string|null $target_url
 * @property string|null $position
 * @property int|null $sort_order
 * @property bool|null $is_active
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Banner extends Model
{
    use SoftDeletes;

    use HasLocalImageUrl;

    protected $table = 'banners';

    protected $casts = [
        'sort_order' => 'int',
        'is_active' => 'bool',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $fillable = [
        'title',
        'image_url',
        'target_url',
        'position',
        'sort_order',
        'is_active',
        'start_date',
        'end_date',
    ];

    public function getImageUrlAttribute(?string $value): ?string
    {
        return $this->resolveImageUrl($value);
    }
}
