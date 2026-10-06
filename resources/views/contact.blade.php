<x-layouts.app title="Contact" description="Contact Temu Estates by WhatsApp, phone, email or message.">
    @php
        $phone = \App\Models\Setting::get('phone'); $email = \App\Models\Setting::get('email'); $address = \App\Models\Setting::get('address');
        $socials = array_filter(['Facebook' => \App\Models\Setting::get('facebook'), 'Instagram' => \App\Models\Setting::get('instagram'), 'Telegram' => \App\Models\Setting::get('telegram')]);
    @endphp
    <section class="border-b border-white/10 bg-neutral-950">
        <div class="mx-auto max-w-5xl px-4 py-12">
            <h1 class="font-display text-5xl font-semibold text-white">Contact Temu Estates</h1>
            <p class="mt-3">The fastest way to reach us is WhatsApp.</p>
        </div>
    </section>
    <div class="mx-auto grid max-w-5xl gap-10 px-4 py-12 md:grid-cols-2">
        <div class="space-y-4">
            <x-whatsapp-button class="w-full justify-center px-6 py-4 text-lg" />
            @if ($phone)<a href="tel:{{ preg_replace('/\s/', '', $phone) }}" class="block rounded-lg border border-gold px-6 py-4 text-center text-lg font-semibold text-gold hover:bg-gold hover:text-black">Call {{ $phone }}</a>@endif
            <dl class="space-y-3 pt-4">
                @if ($email)<div><dt class="text-sm text-neutral-500">Email</dt><dd><a href="mailto:{{ $email }}" class="text-white hover:text-gold">{{ $email }}</a></dd></div>@endif
                @if ($address)<div><dt class="text-sm text-neutral-500">Office</dt><dd class="text-white">{{ $address }}</dd></div>@endif
                @foreach ($socials as $name => $url)<div><dt class="text-sm text-neutral-500">{{ $name }}</dt><dd><a href="{{ $url }}" target="_blank" rel="noopener" class="text-white hover:text-gold">{{ $url }}</a></dd></div>@endforeach
            </dl>
        </div>
        <div>
            <h2 class="mb-4 font-display text-3xl font-semibold text-white">Send us a message</h2>
            <x-inquiry-form type="general" button="Send message" />
        </div>
    </div>
</x-layouts.app>
