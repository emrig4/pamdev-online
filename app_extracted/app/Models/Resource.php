<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $table = 'resources';

    protected $fillable = [
        'filename',
        'title', 
        'overview',
        'author',
        'coauthors',
        'type',
        'field',
        'sub_fields',
        'currency',
        'price',
        'preview_limit',
        'slug',
        'is_published',
        'is_academic',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'preview_limit' => 'integer',
        'is_published' => 'boolean',
        'is_academic' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeAcademic($query)
    {
        return $query->where('is_academic', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getFormattedPriceAttribute()
    {
        $symbols = [
            'NGN' => '₦',
            'USD' => '$', 
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CAD' => 'C$',
            'AUD' => 'A$'
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;
        return $symbol . number_format($this->price, 2);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($resource) {
            if (empty($resource->slug)) {
                $slug = \Str::slug($resource->title);
                $originalSlug = $slug;
                $counter = 1;

                while (static::where('slug', $slug)->where('id', '!=', $resource->id ?? 0)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $resource->slug = $slug;
            }

            if ($resource->is_academic ?? false) {
                $resource->is_published = true;
            }
        });

        static::updating(function ($resource) {
            if ($resource->isDirty('title')) {
                $slug = \Str::slug($resource->title);
                $originalSlug = $slug;
                $counter = 1;

                while (static::where('slug', $slug)->where('id', '!=', $resource->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $resource->slug = $slug;
            }
        });
    }
}
