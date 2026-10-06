<x-layouts.app title="Buy, rent and invest in Addis Ababa property">

{{-- Hero: drop a photo at public/images/hero.jpg and it will be used automatically. --}}
@php $hero = file_exists(public_path('images/hero.jpg')); @endphp
<section class="relative overflow-hidden border-b border-gold/20"
         @if ($hero) style="background: linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)), url('{{ asset('images/hero.jpg') }}') center/cover" @endif>
    @unless ($hero)
        <div class="pointer-events-none absolute -right-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-gold/10 blur-3xl"></div>
    @endunless
    <div class="relative mx-auto max-w-7xl px-4 pb-14 pt-16 md:pb-20 md:pt-24">
        <p class="text-sm text-gold">Temu Estates &middot; Addis Ababa</p>
        <h1 class="mt-3 max-w-3xl font-display text-5xl font-semibold leading-[1.05] text-white md:text-7xl">Building trust. <span class="text-gold">Building futures.</span></h1>
        <p class="mt-5 max-w-xl text-lg text-neutral-300">Helping you find, rent, sell and invest in properties in Ethiopia.</p>

        {{-- Search --}}
        <form x-data="{ mode: 'buy' }" :action="mode === 'buy' ? '{{ route('buy') }}' : '{{ route('rent') }}'" method="GET"
              class="mt-10 rounded-2xl border border-gold/30 bg-black/70 p-4 backdrop-blur md:p-5">
            <div class="mb-4 inline-flex rounded-lg border border-white/15 p-1">
                <button type="button" @click="mode = 'buy'" :class="mode === 'buy' ? 'bg-gold text-black' : 'text-neutral-300'" class="rounded-md px-5 py-2 text-sm font-semibold">Buy</button>
                <button type="button" @click="mode = 'rent'" :class="mode === 'rent' ? 'bg-gold text-black' : 'text-neutral-300'" class="rounded-md px-5 py-2 text-sm font-semibold">Rent</button>
            </div>
            @php $sel = 'w-full rounded-lg border border-white/15 bg-neutral-900 px-3 py-3 text-white focus:border-gold focus:outline-none'; @endphp
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <select name="location" class="{{ $sel }}" aria-label="Location">
                    <option value="">Any location</option>
                    @foreach ($locations as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach
                </select>
                <select name="type" class="{{ $sel }}" aria-label="Property type">
                    <option value="">Any property type</option>
                    @foreach ($types as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                </select>
                <select name="bedrooms" class="{{ $sel }}" aria-label="Bedrooms">
                    <option value="">Any bedrooms</option>
                    @foreach ([1, 2, 3, 4, 5] as $n)<option value="{{ $n }}">{{ $n }}+ bedrooms</option>@endforeach
                </select>
                <input name="max_price" type="number" inputmode="numeric" min="0" placeholder="Max price (ETB)" class="{{ $sel }} placeholder-neutral-500" aria-label="Maximum price">
                <button class="rounded-lg bg-gold py-3 font-semibold text-black transition hover:bg-gold-light">Search</button>
            </div>
        </form>

        <div class="mt-6 flex flex-wrap gap-3">
            <x-whatsapp-button label="Chat with an agent on WhatsApp" class="px-5 py-3" />
            <a href="{{ route('list.create') }}" class="rounded-lg border border-white/25 px-5 py-3 font-semibold text-white hover:border-gold hover:text-gold">List your property</a>
        </div>
    </div>
</section>

{{-- Featured --}}
<section class="mx-auto max-w-7xl px-4 pt-16">
    <div class="flex items-end justify-between">
        <h2 class="font-display text-4xl font-semibold text-white">Featured properties</h2>
        <a href="{{ route('buy') }}" class="text-sm text-gold hover:underline">See all properties</a>
    </div>
    @if ($featured->isEmpty())
        <p class="mt-6 text-neutral-400">New listings are on the way. In the meantime, <a href="{{ route('buy') }}" class="text-gold underline">browse all properties</a>.</p>
    @else
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featured as $property)<x-property-card :property="$property" />@endforeach
        </div>
    @endif
</section>

{{-- Diaspora --}}
<section class="mx-auto mt-20 max-w-7xl px-4">
    <div class="rounded-2xl border border-gold/30 bg-gradient-to-br from-neutral-900 to-black p-8 md:p-14">
        <h2 class="max-w-2xl font-display text-4xl font-semibold text-white md:text-5xl">Looking for property in Ethiopia from abroad?</h2>
        <p class="mt-4 max-w-xl text-lg">Temu Estates helps clients abroad discover and evaluate property opportunities in Addis Ababa.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('buy') }}" class="rounded-lg bg-gold px-6 py-3 font-semibold text-black hover:bg-gold-light">Explore properties</a>
            <x-whatsapp-button label="Talk to an agent" message="Hello Temu Estates, I live abroad and I'm looking for property in Addis Ababa." class="border border-gold !bg-transparent px-6 py-3 !text-gold hover:!bg-gold hover:!text-black" />
        </div>
    </div>
</section>

{{-- Services --}}
<section class="mx-auto mt-20 max-w-7xl px-4">
    <h2 class="font-display text-4xl font-semibold text-white">What we do</h2>
    <p class="mt-3 max-w-2xl">We help people buy, rent, sell and find property across Addis Ababa.</p>
    <div class="mt-8 grid gap-px overflow-hidden rounded-xl border border-white/10 bg-white/10 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            'Property sales' => 'Find the right home or investment, and close with confidence.',
            'Property rentals' => 'Quality homes and offices for rent, with clear terms.',
            'Property marketing' => 'We present your property professionally to the right buyers and tenants.',
            'Property sourcing' => 'Tell us what you need and we will find it.',
            'Commercial property' => 'Shops, offices, buildings, warehouses and land.',
            'Property consultancy' => 'Straight advice on prices, locations and investment.',
        ] as $name => $text)
            <div class="bg-black p-6">
                <h3 class="text-lg font-semibold text-gold">{{ $name }}</h3>
                <p class="mt-2 text-sm text-neutral-400">{{ $text }}</p>
            </div>
        @endforeach
    </div>
    <a href="{{ route('about') }}" class="mt-6 inline-block text-gold hover:underline">About Temu Estates</a>
</section>

{{-- Contact --}}
<section class="mx-auto mt-20 max-w-3xl px-4 text-center">
    <h2 class="font-display text-4xl font-semibold text-white">Ready to talk?</h2>
    <p class="mt-3">Message us on WhatsApp, call, or request a viewing. We reply fast.</p>
    <div class="mt-6 flex flex-wrap justify-center gap-3">
        <x-whatsapp-button class="px-6 py-3" />
        @if ($phone = \App\Models\Setting::get('phone'))
            <a href="tel:{{ preg_replace('/\s/', '', $phone) }}" class="rounded-lg border border-gold px-6 py-3 font-semibold text-gold hover:bg-gold hover:text-black">Call {{ $phone }}</a>
        @endif
        <a href="{{ route('contact') }}" class="rounded-lg border border-white/25 px-6 py-3 font-semibold text-white hover:border-gold hover:text-gold">Send a message</a>
    </div>
</section>

</x-layouts.app>
