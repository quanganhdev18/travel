<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasTranslations;
    use SoftDeletes;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'order',
        'is_active',
    ];

    public array $translatable = [
        'question',
        'answer',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }

    public function scopeByCategory($query, ?string $category = null)
    {
        if ($category) {
            return $query->where('category', $category);
        }

        return $query;
    }
}
