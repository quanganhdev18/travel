<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $phone
 * @property string|null $role
 * @property string|null $preferences
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection|Booking[] $bookings
 * @property Collection|Invoice[] $invoices
 * @property Collection|Review[] $reviews
 * @property Collection|TicketBooking[] $ticket_bookings
 * @property Collection|UserAddress[] $user_addresses
 * @property UserIdentity|null $user_identity
 * @property Collection|Wishlist[] $wishlists
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'users';

    protected $hidden = [
        'password',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'preferences',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Role checking methods
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN->value;
    }

    public function isStaff(): bool
    {
        return $this->role === UserRole::STAFF->value;
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::CUSTOMER->value;
    }

    public function hasRole(string|UserRole $role): bool
    {
        $roleValue = $role instanceof UserRole ? $role->value : $role;

        return $this->role === $roleValue;
    }

    public function hasAnyRole(array $roles): bool
    {
        $roleValues = collect($roles)->map(function ($role) {
            return $role instanceof UserRole ? $role->value : $role;
        })->toArray();

        return in_array($this->role, $roleValues);
    }

    public function canAccessAdmin(): bool
    {
        return $this->hasAnyRole([UserRole::ADMIN, UserRole::STAFF]);
    }

    public function getRoleLabelAttribute(): string
    {
        return UserRole::tryFrom($this->role)?->label() ?? 'Không xác định';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Hoạt động' : 'Bị khóa';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'danger';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function ticket_bookings()
    {
        return $this->hasMany(TicketBooking::class);
    }

    public function user_addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function user_identity()
    {
        return $this->hasOne(UserIdentity::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function identity()
    {
        return $this->hasOne(UserIdentity::class);
    }
}
