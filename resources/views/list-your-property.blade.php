<x-layouts.app title="List your property" description="Have a property to sell or rent? Let Temu Estates market it and connect you with potential clients.">
    @php $f = 'w-full rounded-lg border border-white/15 bg-neutral-900 px-3 py-3 text-white placeholder-neutral-500 focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold'; @endphp
    <section class="border-b border-white/10 bg-neutral-950">
        <div class="mx-auto max-w-3xl px-4 py-12">
            <h1 class="font-display text-4xl font-semibold text-white md:text-5xl">Have a property to sell or rent?</h1>
            <p class="mt-3 text-lg">Let Temu Estates help you market your property and connect you with potential clients. Send us the details below. Our team reviews every submission before it is published.</p>
        </div>
    </section>

    <div class="mx-auto max-w-3xl px-4 py-10">
        <form method="POST" action="{{ route('list.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <input type="text" name="website" tabindex="-1" autocomplete="off" class="absolute -left-[9999px] h-0 w-0 opacity-0" aria-hidden="true">

            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="mb-1 block text-sm">Your name</label><input name="name" value="{{ old('name') }}" required class="{{ $f }}">@error('name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror</div>
                <div><label class="mb-1 block text-sm">Phone number</label><input name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" required class="{{ $f }}">@error('phone')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror</div>
                <div><label class="mb-1 block text-sm">WhatsApp number</label><input name="whatsapp" type="tel" inputmode="tel" value="{{ old('whatsapp') }}" class="{{ $f }}">@error('whatsapp')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror</div>
                <div><label class="mb-1 block text-sm">Property location</label><input name="location" value="{{ old('location') }}" placeholder="e.g. Bole, near Edna Mall" required class="{{ $f }}">@error('location')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror</div>
                <div>
                    <label class="mb-1 block text-sm">Property type</label>
                    <select name="property_type_id" required class="{{ $f }}">
                        <option value="">Choose a type</option>
                        @foreach ($types as $t)<option value="{{ $t->id }}" @selected(old('property_type_id') == $t->id)>{{ $t->name }}</option>@endforeach
                    </select>
                    @error('property_type_id')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm">Sale or rent</label>
                    <select name="listing_type" required class="{{ $f }}">
                        <option value="sale" @selected(old('listing_type') === 'sale')>Sale</option>
                        <option value="rent" @selected(old('listing_type') === 'rent')>Rent</option>
                    </select>
                </div>
                <div><label class="mb-1 block text-sm">Expected price (ETB)</label><input name="expected_price" type="number" inputmode="numeric" min="0" value="{{ old('expected_price') }}" class="{{ $f }}">@error('expected_price')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror</div>
                <div><label class="mb-1 block text-sm">Bedrooms</label><input name="bedrooms" type="number" inputmode="numeric" min="0" value="{{ old('bedrooms') }}" class="{{ $f }}"></div>
                <div><label class="mb-1 block text-sm">Property size (m&sup2;)</label><input name="size_sqm" type="number" inputmode="decimal" min="0" step="any" value="{{ old('size_sqm') }}" class="{{ $f }}"></div>
            </div>

            <div><label class="mb-1 block text-sm">Description</label><textarea name="description" rows="4" class="{{ $f }}">{{ old('description') }}</textarea></div>
            <div>
                <label class="mb-1 block text-sm">Property photos (up to 10, 5 MB each)</label>
                <input name="photos[]" type="file" accept="image/*" multiple class="{{ $f }} file:mr-3 file:rounded file:border-0 file:bg-gold file:px-3 file:py-1.5 file:font-semibold file:text-black">
                @error('photos')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                @error('photos.*')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div><label class="mb-1 block text-sm">Additional information</label><textarea name="additional_info" rows="3" class="{{ $f }}">{{ old('additional_info') }}</textarea></div>
            @error('website')<p class="text-sm text-red-400">Something went wrong. Please try again.</p>@enderror

            <button class="w-full rounded-lg bg-gold py-4 text-lg font-semibold text-black hover:bg-gold-light">Submit property</button>
        </form>
    </div>
</x-layouts.app>
