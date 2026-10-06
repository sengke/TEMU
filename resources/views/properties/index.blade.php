<x-layouts.app :title="$heading">
    <section class="border-b border-white/10 bg-neutral-950">
        <div class="mx-auto max-w-7xl px-4 py-10">
            <h1 class="font-display text-4xl font-semibold text-white md:text-5xl">{{ $heading }}</h1>
            <p class="mt-2 text-neutral-400">Addis Ababa, Ethiopia</p>
        </div>
    </section>
    <div class="mx-auto max-w-7xl px-4 py-8">
        @livewire('property-search', ['fixedListing' => $listing, 'fixedCategory' => $category])
    </div>
</x-layouts.app>
