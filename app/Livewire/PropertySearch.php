<?php

namespace App\Livewire;

use App\Enums\Furnished;
use App\Enums\PropertyStatus;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyType;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PropertySearch extends Component
{
    use WithPagination;

    // Set by the page (Buy page = 'sale', Rent page = 'rent', Commercial page = category 'commercial').
    public ?string $fixedListing = null;
    public ?string $fixedCategory = null;

    // Filters. Strings everywhere, because empty <select> values arrive as ''.
    #[Url(except: '')] public string $listing = '';
    #[Url(except: '')] public string $category = '';
    #[Url(except: '')] public string $location = '';
    #[Url(except: '')] public string $type = '';
    #[Url(except: '')] public string $bedrooms = '';
    #[Url(except: '')] public string $bathrooms = '';
    #[Url(except: '')] public string $min_price = '';
    #[Url(except: '')] public string $max_price = '';
    #[Url(except: '')] public string $min_size = '';
    #[Url(except: '')] public string $furnished = '';
    #[Url(except: '')] public string $status = '';

    public function updated(): void
    {
        $this->resetPage();
    }

    public function clear(): void
    {
        $this->reset(['listing', 'category', 'location', 'type', 'bedrooms', 'bathrooms', 'min_price', 'max_price', 'min_size', 'furnished', 'status']);
        $this->resetPage();
    }

    public function render()
    {
        $listing = $this->fixedListing ?: $this->listing;
        $category = $this->fixedCategory ?: $this->category;

        $properties = Property::query()
            ->published()
            ->with(['location', 'propertyType', 'media'])
            ->when($listing, fn ($q) => $q->where('listing_type', $listing))
            ->when($category === 'commercial', fn ($q) => $q->commercial())
            ->when($category === 'residential', fn ($q) => $q->residential())
            ->when($this->location, fn ($q) => $q->where('location_id', $this->location))
            ->when($this->type, fn ($q) => $q->where('property_type_id', $this->type))
            ->when($this->bedrooms !== '', fn ($q) => $q->where('bedrooms', '>=', (int) $this->bedrooms))
            ->when($this->bathrooms !== '', fn ($q) => $q->where('bathrooms', '>=', (int) $this->bathrooms))
            ->when($this->min_price !== '', fn ($q) => $q->where('price', '>=', (float) $this->min_price))
            ->when($this->max_price !== '', fn ($q) => $q->where('price', '<=', (float) $this->max_price))
            ->when($this->min_size !== '', fn ($q) => $q->where('size_sqm', '>=', (float) $this->min_size))
            ->when($this->furnished, fn ($q) => $q->where('furnished', $this->furnished))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            // Available listings first, sold/rented ones last.
            ->orderByRaw("CASE WHEN status IN ('sold','rented') THEN 1 ELSE 0 END")
            ->latest()
            ->paginate(9);

        return view('livewire.property-search', [
            'properties' => $properties,
            'locations' => Location::orderBy('name')->get(),
            'types' => PropertyType::when($category, fn ($q) => $q->where('category', $category))->orderBy('name')->get(),
            'statuses' => PropertyStatus::cases(),
            'furnishedOptions' => Furnished::cases(),
            'showListingFilter' => ! $this->fixedListing,
            'showCategoryFilter' => ! $this->fixedCategory,
        ]);
    }
}
