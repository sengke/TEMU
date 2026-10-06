@php
    $phone = \App\Models\Setting::get('phone');
    $isRent = $property->listing_type === \App\Enums\ListingType::Rent;
    $images = $property->getMedia('gallery')->map(fn ($m) => ['large' => $m->getUrl('large'), 'thumb' => $m->getUrl('thumb')])->values();
    $floorplan = $property->getFirstMedia('floorplan');
    $uploadedVideo = $property->getFirstMedia('video');
    $embed = $property->videoEmbedUrl();
    $hasMap = $property->latitude && $property->longitude;
    $facts = array_filter([
        'Property type' => $property->propertyType->name,
        'Bedrooms' => $property->bedrooms,
        'Bathrooms' => $property->bathrooms,
        'Size' => $property->size_sqm ? rtrim(rtrim(number_format($property->size_sqm, 1), '0'), '.') . ' m²' : null,
        'Floor' => $property->floor,
        'Parking' => $property->has_parking ? 'Yes' : 'No',
        'Furnishing' => $property->furnished?->label(),
        'Completion' => $property->completion_status,
        'Available from' => $property->available_from?->format('j M Y'),
        'Listing' => $property->listing_type->label(),
    ], fn ($v) => ! is_null($v) && $v !== '');
@endphp
<x-layouts.app :title="$property->title . ' - ' . $property->location->name" :description="\Illuminate\Support\Str::limit(strip_tags($property->description ?? ''), 155)" :image="$images->first()['large'] ?? null">

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateListing',
            'name' => $property->title,
            'url' => url()->current(),
            'description' => strip_tags($property->description ?? ''),
            'image' => $images->pluck('large')->all(),
            'offers' => ['@type' => 'Offer', 'price' => (float) $property->price, 'priceCurrency' => $property->currency],
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => $property->location->name, 'addressRegion' => 'Addis Ababa', 'addressCountry' => 'ET'],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>

    {{-- On phones, the bottom bar shows this property's contact buttons --}}
    <x-slot:bar>
        <div class="grid grid-cols-3 gap-2 text-sm">
            <a href="{{ $property->whatsappUrl() }}" target="_blank" rel="noopener" class="rounded-lg bg-gold py-3 text-center font-semibold text-black">WhatsApp</a>
            @if ($phone)
                <a href="tel:{{ preg_replace('/\s/', '', $phone) }}" class="rounded-lg border border-gold py-3 text-center font-semibold text-gold">Call</a>
            @endif
            <a href="#contact" class="rounded-lg border border-white/30 py-3 text-center font-semibold text-white">Request viewing</a>
        </div>
    </x-slot:bar>

    <div class="mx-auto max-w-7xl px-4 py-6">
        <a href="{{ route($isRent ? 'rent' : 'buy') }}" class="text-sm text-neutral-400 hover:text-gold">&larr; Back to {{ $isRent ? 'rentals' : 'properties for sale' }}</a>

        <div class="mt-4 grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">

                {{-- Gallery --}}
                <div x-data='{ i: 0, imgs: @json($images, JSON_HEX_APOS) }'>
                    <div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-neutral-900 md:aspect-[16/10]">
                        <template x-if="imgs.length"><img :src="imgs[i].large" alt="{{ $property->title }}" class="h-full w-full object-cover"></template>
                        <template x-if="!imgs.length"><div class="flex h-full items-center justify-center text-gold/40">Photos coming soon</div></template>
                        <x-status-badge :status="$property->status" class="absolute left-4 top-4 !text-sm" />
                        <template x-if="imgs.length > 1">
                            <div>
                                <button @click="i = (i - 1 + imgs.length) % imgs.length" class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-black/70 px-3 py-2 text-white" aria-label="Previous photo">&lsaquo;</button>
                                <button @click="i = (i + 1) % imgs.length" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-black/70 px-3 py-2 text-white" aria-label="Next photo">&rsaquo;</button>
                            </div>
                        </template>
                    </div>
                    <div class="mt-2 flex gap-2 overflow-x-auto pb-1" x-show="imgs.length > 1">
                        <template x-for="(img, n) in imgs" :key="n">
                            <button @click="i = n" class="h-16 w-24 shrink-0 overflow-hidden rounded-md border-2" :class="i === n ? 'border-gold' : 'border-transparent opacity-70'">
                                <img :src="img.thumb" alt="" loading="lazy" class="h-full w-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Title + price --}}
                <h1 class="mt-6 font-display text-4xl font-semibold text-white md:text-5xl">{{ $property->title }}</h1>
                <p class="mt-1 text-neutral-400">{{ $property->location->name }}, Addis Ababa</p>
                <p class="mt-3 font-display text-4xl font-semibold text-gold">{{ $property->priceLabel() }}</p>

                {{-- Key facts --}}
                <dl class="mt-6 grid grid-cols-2 gap-px overflow-hidden rounded-xl border border-white/10 bg-white/10 sm:grid-cols-3">
                    @foreach ($facts as $label => $value)
                        <div class="bg-neutral-950 p-4">
                            <dt class="text-xs text-neutral-500">{{ $label }}</dt>
                            <dd class="mt-1 font-semibold text-white">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if ($property->description)
                    <h2 class="mt-10 font-display text-3xl font-semibold text-white">Description</h2>
                    <div class="mt-3 max-w-prose space-y-3 leading-relaxed">{!! nl2br(e($property->description)) !!}</div>
                @endif

                @if ($property->amenities->isNotEmpty())
                    <h2 class="mt-10 font-display text-3xl font-semibold text-white">Amenities</h2>
                    <ul class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ($property->amenities as $a)
                            <li class="rounded-lg border border-white/10 px-3 py-2 text-sm"><span class="text-gold">&#10003;</span> {{ $a->name }}</li>
                        @endforeach
                    </ul>
                @endif

                @if ($embed || $uploadedVideo)
                    <h2 class="mt-10 font-display text-3xl font-semibold text-white">Video</h2>
                    <div class="mt-3 aspect-video overflow-hidden rounded-xl bg-neutral-900">
                        @if ($embed)
                            <iframe src="{{ $embed }}" class="h-full w-full" loading="lazy" allowfullscreen title="Property video"></iframe>
                        @else
                            <video src="{{ $uploadedVideo->getUrl() }}" controls preload="none" class="h-full w-full"></video>
                        @endif
                    </div>
                @endif

                @if ($floorplan)
                    <h2 class="mt-10 font-display text-3xl font-semibold text-white">Floor plan</h2>
                    <a href="{{ $floorplan->getUrl() }}" target="_blank" class="mt-3 block"><img src="{{ $floorplan->getUrl() }}" alt="Floor plan" loading="lazy" class="max-h-96 rounded-xl border border-white/10 bg-white object-contain"></a>
                @endif

                @if ($hasMap)
                    <h2 class="mt-10 font-display text-3xl font-semibold text-white">Location</h2>
                    <p class="mt-1 text-sm text-neutral-500">Approximate area. The exact address is shared when you book a viewing.</p>
                    <div id="map" class="mt-3 h-72 overflow-hidden rounded-xl"></div>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
                    <script>
                        window.addEventListener('load', function () {
                            var pos = [{{ $property->latitude }}, {{ $property->longitude }}];
                            var map = L.map('map', { scrollWheelZoom: false }).setView(pos, 15);
                            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
                            L.circle(pos, { radius: 300, color: '#C9A24B', fillOpacity: 0.2 }).addTo(map);
                        });
                    </script>
                @endif
            </div>

            {{-- Contact panel --}}
            <aside id="contact" class="lg:sticky lg:top-20 lg:self-start">
                <div class="rounded-xl border border-gold/30 bg-neutral-950 p-5" x-data="{ tab: 'viewing' }">
                    <p class="font-display text-2xl font-semibold text-gold">{{ $property->priceLabel() }}</p>
                    @unless ($property->status->isAvailable())
                        <p class="mt-1 text-sm text-amber-300">This property is {{ strtolower($property->status->label()) }}. Contact us for similar options.</p>
                    @endunless

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <a href="{{ $property->whatsappUrl() }}" target="_blank" rel="noopener" class="rounded-lg bg-gold py-3 text-center font-semibold text-black hover:bg-gold-light">WhatsApp agent</a>
                        @if ($phone)
                            <a href="tel:{{ preg_replace('/\s/', '', $phone) }}" class="rounded-lg border border-gold py-3 text-center font-semibold text-gold hover:bg-gold hover:text-black">Call agent</a>
                        @endif
                    </div>

                    <div class="mt-5 flex border-b border-white/10 text-sm">
                        <button @click="tab = 'viewing'" :class="tab === 'viewing' ? 'border-gold text-gold' : 'border-transparent'" class="flex-1 border-b-2 py-2 font-semibold">Request viewing</button>
                        <button @click="tab = 'inquiry'" :class="tab === 'inquiry' ? 'border-gold text-gold' : 'border-transparent'" class="flex-1 border-b-2 py-2 font-semibold">Send inquiry</button>
                    </div>
                    <div class="mt-4" x-show="tab === 'viewing'"><x-inquiry-form type="viewing" :property="$property" button="Request viewing" :with-date="true" /></div>
                    <div class="mt-4" x-show="tab === 'inquiry'" x-cloak><x-inquiry-form type="inquiry" :property="$property" button="Send inquiry" /></div>
                </div>
            </aside>
        </div>

        @if ($similar->isNotEmpty())
            <h2 class="mt-16 font-display text-3xl font-semibold text-white">Similar properties in {{ $property->location->name }}</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($similar as $s)<x-property-card :property="$s" />@endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
