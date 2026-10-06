@props(['type' => 'inquiry', 'property' => null, 'button' => 'Send inquiry', 'withDate' => false])
<form method="POST" action="{{ route('inquiry.store') }}" class="space-y-3">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    @if ($property)<input type="hidden" name="property_id" value="{{ $property->id }}">@endif
    {{-- Honeypot: hidden from people, bots fill it in --}}
    <input type="text" name="website" tabindex="-1" autocomplete="off" class="absolute -left-[9999px] h-0 w-0 opacity-0" aria-hidden="true">

    @php $field = 'w-full rounded-lg border border-white/15 bg-neutral-900 px-3 py-3 text-white placeholder-neutral-500 focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold'; @endphp

    <div>
        <input name="name" value="{{ old('name') }}" placeholder="Your name" autocomplete="name" required class="{{ $field }}">
        @error('name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
    </div>
    <div>
        <input name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" placeholder="Phone number" autocomplete="tel" required class="{{ $field }}">
        @error('phone')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
    </div>
    <div>
        <input name="whatsapp" type="tel" inputmode="tel" value="{{ old('whatsapp') }}" placeholder="WhatsApp number (if different)" class="{{ $field }}">
        @error('whatsapp')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
    </div>
    @if ($withDate)
        <div>
            <label class="mb-1 block text-sm text-neutral-400">Preferred viewing date</label>
            <input name="preferred_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('preferred_date') }}" class="{{ $field }}">
            @error('preferred_date')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
        </div>
    @endif
    <textarea name="message" rows="3" placeholder="Your message" class="{{ $field }}">{{ old('message') }}</textarea>
    @error('website')<p class="text-sm text-red-400">Something went wrong. Please try again.</p>@enderror

    <button class="w-full rounded-lg bg-gold py-3 font-semibold text-black transition hover:bg-gold-light">{{ $button }}</button>
</form>
