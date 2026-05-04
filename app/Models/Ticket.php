<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ticket
 * 
 * @property int $id
 * @property int $destination_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string|null $provider_name
 * @property string|null $cancellation_policy
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Destination $destination
 * @property Collection|TicketImage[] $ticket_images
 * @property Collection|TicketOption[] $ticket_options
 *
 * @package App\Models
 */
class Ticket extends Model
{
	protected $table = 'tickets';

	protected $casts = [
		'destination_id' => 'int'
	];

	protected $fillable = [
		'destination_id',
		'title',
		'slug',
		'description',
		'provider_name',
		'cancellation_policy'
	];

	public function destination()
	{
		return $this->belongsTo(Destination::class);
	}

	public function ticket_images()
	{
		return $this->hasMany(TicketImage::class);
	}

	public function ticket_options()
	{
		return $this->hasMany(TicketOption::class);
	}
}
