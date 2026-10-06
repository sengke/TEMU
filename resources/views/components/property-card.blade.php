@props(['property'])
@php $isRent = $property->listing_type === \App\Enums\ListingType::Rent; @endphp
<article {{ $attributes->class(["group flex flex-col overflow-hidden rounded-xl border border-white/10 bg-neutral-950 transition hover:border-gold/60"]) }}>
    <a href="{{ route('properties.show', $property) }}" class="relative block aspect-[4/3] overflow-hidden bg-neutral-900">
        @if ($img = $property->coverUrl())
            <img src="{{ $img }}" alt="{{ $property->title }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center text-sm text-gold/40">Photos coming soon</div>
        @endif
        <x-status-badge :status="$property->status" class="absolute left-3 top-3" />
    </a>

    <div class="flex flex-1 flex-col p-4">
        <p class="font-display text-2xl font-semibold text-gold">{{ $property->priceLabel() }}</p>
        <h3 class="mt-1 text-lg font-semibold text-white">{{ $property->title }}</h3>
        <p class="text-sm text-neutral-400">{{ $property->location->name }}, Addis Ababa</p>

        <ul class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm text-neutral-300">
            <li>{{ $property->propertyType->name }}</li>
            @if (! is_null($property->bedrooms))<li>{{ $property->bedrooms }} bed</li>@endif
            @if (! is_null($property->bathrooms))<li>{{ $property->bathrooms }} bath</li>@endif
            @if ($property->size_sqm)<li>{{ rtrim(rtrim(number_format($property->size_sqm, 1), '0'), '.') }} m&sup2;</li>@endif
            @if ($isRent && $property->has_parking)<li>Parking</li>@endif
            @if ($isRent && $property->furnished)<li>{{ $property->furnished->label() }}</li>@endif
        </ul>

        @if ($isRent && $property->available_from)
            <p class="mt-2 text-xs text-neutral-500">Available from {{ $property->available_from->format('j M Y') }}</p>
        @endif

        @if ($property->description)
            <p class="mt-3 line-clamp-2 text-sm text-neutral-400">{{ \Illuminate\Support\Str::limit(strip_tags($property->description), 110) }}</p>
        @endif

        <a href="{{ route('properties.show', $property) }}" class="mt-auto pt-4">
            <span class="block rounded-lg border border-gold py-2.5 text-center font-semibold text-gold transition group-hover:bg-gold group-hover:text-black">View property</span>
        </a>
    </div>
</article>
