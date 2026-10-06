<div x-data="{ filters: false }">
    @php
        $f = 'w-full rounded-lg border border-white/15 bg-neutral-900 px-3 py-2.5 text-white focus:border-gold focus:outline-none';
        $active = collect([$listing, $category, $location, $type, $bedrooms, $bathrooms, $min_price, $max_price, $min_size, $furnished, $status])->filter(fn ($v) => $v !== '')->count();
    @endphp

    {{-- Filter toggle (mobile) --}}
    <button type="button" @click="filters = !filters" class="mb-4 flex w-full items-center justify-between rounded-lg border border-gold/40 px-4 py-3 text-gold lg:hidden">
        <span>Filters @if ($active)({{ $active }})@endif</span>
        <span x-text="filters ? 'Hide' : 'Show'">Show</span>
    </button>

    <div :class="filters ? 'block' : 'hidden'" class="mb-8 rounded-xl border border-white/10 bg-neutral-950 p-4 lg:block">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @if ($showListingFilter)
                <select wire:model.live="listing" class="{{ $f }}" aria-label="Buy or rent">
                    <option value="">Buy or rent</option>
                    <option value="sale">Buy</option>
                    <option value="rent">Rent</option>
                </select>
            @endif
            @if ($showCategoryFilter)
                <select wire:model.live="category" class="{{ $f }}" aria-label="Residential or commercial">
                    <option value="">Residential and commercial</option>
                    <option value="residential">Residential</option>
                    <option value="commercial">Commercial</option>
                </select>
            @endif
            <select wire:model.live="location" class="{{ $f }}" aria-label="Location">
                <option value="">Any location</option>
                @foreach ($locations as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach
            </select>
            <select wire:model.live="type" class="{{ $f }}" aria-label="Property type">
                <option value="">Any property type</option>
                @foreach ($types as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
            </select>
            <select wire:model.live="bedrooms" class="{{ $f }}" aria-label="Bedrooms">
                <option value="">Any bedrooms</option>
                @foreach ([1, 2, 3, 4, 5] as $n)<option value="{{ $n }}">{{ $n }}+ bedrooms</option>@endforeach
            </select>
            <select wire:model.live="bathrooms" class="{{ $f }}" aria-label="Bathrooms">
                <option value="">Any bathrooms</option>
                @foreach ([1, 2, 3, 4] as $n)<option value="{{ $n }}">{{ $n }}+ bathrooms</option>@endforeach
            </select>
            <input wire:model.live.debounce.600ms="min_price" type="number" inputmode="numeric" min="0" placeholder="Min price (ETB)" class="{{ $f }} placeholder-neutral-500">
            <input wire:model.live.debounce.600ms="max_price" type="number" inputmode="numeric" min="0" placeholder="Max price (ETB)" class="{{ $f }} placeholder-neutral-500">
            <input wire:model.live.debounce.600ms="min_size" type="number" inputmode="numeric" min="0" placeholder="Min size (m&sup2;)" class="{{ $f }} placeholder-neutral-500">
            <select wire:model.live="furnished" class="{{ $f }}" aria-label="Furnished">
                <option value="">Furnished or not</option>
                @foreach ($furnishedOptions as $o)<option value="{{ $o->value }}">{{ $o->label() }}</option>@endforeach
            </select>
            <select wire:model.live="status" class="{{ $f }}" aria-label="Status">
                <option value="">Any status</option>
                @foreach ($statuses as $s)<option value="{{ $s->value }}">{{ $s->label() }}</option>@endforeach
            </select>
            <button type="button" wire:click="clear" class="rounded-lg border border-white/20 px-3 py-2.5 text-neutral-300 hover:border-gold hover:text-gold">Clear filters</button>
        </div>
    </div>

    <p class="mb-4 text-sm text-neutral-400" wire:loading.remove>{{ $properties->total() }} {{ \Illuminate\Support\Str::plural('property', $properties->total()) }} found</p>
    <p class="mb-4 text-sm text-gold" wire:loading>Updating results...</p>

    @if ($properties->isEmpty())
        <div class="rounded-xl border border-white/10 bg-neutral-950 p-10 text-center">
            <p class="text-lg text-white">No properties match these filters.</p>
            <p class="mt-2 text-neutral-400">Try widening your price range or clearing a filter, or tell us what you need and we will search for you.</p>
            <x-whatsapp-button label="Ask an agent" message="Hello Temu Estates, I couldn't find what I'm looking for. Can you help me find a property?" class="mt-5 px-5 py-3" />
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($properties as $property)
                <x-property-card :property="$property" wire:key="p-{{ $property->id }}" />
            @endforeach
        </div>
        <div class="mt-8">{{ $properties->links() }}</div>
    @endif
</div>
