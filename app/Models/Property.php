<?php

namespace App\Models;

use App\Enums\Furnished;
use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use App\Support\Whatsapp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Property extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'listing_type' => ListingType::class,
            'status' => PropertyStatus::class,
            'furnished' => Furnished::class,
            'available_from' => 'date',
            'has_parking' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // If nobody picked a status, choose the sensible one from the listing type.
        static::creating(function (Property $p) {
            if (! $p->status) {
                $p->status = $p->listing_type === ListingType::Rent
                    ? PropertyStatus::ForRent
                    : PropertyStatus::ForSale;
            }
        });
    }

    // ---- URLs, slugs ----
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug')->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ---- Media (photos, floor plan, optional uploaded video) ----
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
        $this->addMediaCollection('floorplan')->singleFile();
        $this->addMediaCollection('video')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 640, 420)->format('webp')
            ->performOnCollections('gallery')->nonQueued();

        $this->addMediaConversion('large')
            ->fit(Fit::Max, 1600, 1200)->format('webp')
            ->performOnCollections('gallery')->nonQueued();
    }

    // ---- Relationships ----
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    // ---- Scopes: reusable query shortcuts ----
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }

    public function scopeCommercial(Builder $q): Builder
    {
        return $q->whereHas('propertyType', fn ($t) => $t->where('category', 'commercial'));
    }

    public function scopeResidential(Builder $q): Builder
    {
        return $q->whereHas('propertyType', fn ($t) => $t->where('category', 'residential'));
    }

    // ---- Helpers used by the views ----
    public function priceLabel(): string
    {
        $price = number_format((float) $this->price) . ' ' . $this->currency;

        return $this->listing_type === ListingType::Rent ? $price . '/month' : $price;
    }

    public function coverUrl(): ?string
    {
        return $this->getFirstMediaUrl('gallery', 'thumb') ?: null;
    }

    public function whatsappUrl(): string
    {
        return Whatsapp::url("Hello Temu Estates, I'm interested in: {$this->title} - " . route('properties.show', $this));
    }

    /** Turn a YouTube link into an embeddable URL (null if not YouTube). */
    public function videoEmbedUrl(): ?string
    {
        if ($this->video_url && preg_match('~(?:youtu\.be/|v=|embed/)([\w-]{11})~', $this->video_url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        return null;
    }
}
