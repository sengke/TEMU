<x-layouts.app title="About" description="Temu Estates helps you buy, rent, sell and invest in property in Addis Ababa, Ethiopia.">
    <section class="border-b border-white/10 bg-neutral-950">
        <div class="mx-auto max-w-3xl px-4 py-14">
            <h1 class="font-display text-5xl font-semibold text-white">About Temu Estates</h1>
            <p class="mt-4 text-xl text-gold">Building trust. Building futures.</p>
        </div>
    </section>
    <div class="mx-auto max-w-3xl space-y-10 px-4 py-12 leading-relaxed">
        <section>
            <h2 class="font-display text-3xl font-semibold text-white">Who we are</h2>
            <p class="mt-3">Temu Estates is a real estate company in Addis Ababa. We help local buyers and renters, Ethiopians abroad, investors and property owners buy, rent, sell and manage property with confidence.</p>
        </section>
        <section>
            <h2 class="font-display text-3xl font-semibold text-white">Why work with us</h2>
            <ul class="mt-3 list-disc space-y-2 pl-5">
                <li>Honest advice on prices, locations and risks</li>
                <li>Every listing is reviewed by our team before it is published</li>
                <li>Fast replies on WhatsApp and phone</li>
                <li>Support for clients abroad, from the first search to the viewing</li>
            </ul>
        </section>
        <section>
            <h2 class="font-display text-3xl font-semibold text-white">Our services</h2>
            <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                @foreach (['Property sales', 'Property rentals', 'Property marketing', 'Property sourcing', 'Commercial property', 'Property consultancy'] as $s)
                    <li class="rounded-lg border border-white/10 px-4 py-3"><span class="text-gold">&#10003;</span> {{ $s }}</li>
                @endforeach
            </ul>
        </section>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('buy') }}" class="rounded-lg bg-gold px-6 py-3 font-semibold text-black hover:bg-gold-light">Browse properties</a>
            <x-whatsapp-button label="Talk to an agent" class="px-6 py-3" />
        </div>
    </div>
</x-layouts.app>
