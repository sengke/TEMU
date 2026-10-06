@props(['title' => null, 'description' => null, 'image' => null])
@php
    $phone = \App\Models\Setting::get('phone');
    $nav = ['home' => 'Home', 'buy' => 'Buy', 'rent' => 'Rent', 'commercial' => 'Commercial', 'list.create' => 'List your property', 'about' => 'About', 'contact' => 'Contact'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' | ' : '' }}Temu Estates</title>
    <meta name="description" content="{{ $description ?? 'Buy, rent, sell and invest in property in Addis Ababa, Ethiopia with Temu Estates. Building trust. Building futures.' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:site_name" content="Temu Estates">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ? $title . ' | Temu Estates' : 'Temu Estates' }}">
    <meta property="og:description" content="{{ $description ?? 'Buy, rent, sell and invest in property in Addis Ababa, Ethiopia.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($image)<meta property="og:image" content="{{ $image }}">@endif
    <meta name="theme-color" content="#000000">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Tailwind via CDN: perfect for development. Before launch we switch to the built version (Vite). --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { gold: { DEFAULT: '#C9A24B', light: '#E3C77A', dark: '#9C7A2E' } },
            fontFamily: { display: ['"Cormorant Garamond"', 'Georgia', 'serif'], sans: ['Figtree', 'system-ui', 'sans-serif'] }
        } } }
    </script>
    <style>[x-cloak]{display:none !important}</style>
    @livewireStyles
</head>
<body class="bg-black font-sans text-neutral-300 antialiased pb-20 md:pb-0">

<header class="sticky top-0 z-40 border-b border-white/10 bg-black/90 backdrop-blur" x-data="{ open: false }">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
        <a href="{{ route('home') }}" class="font-display text-2xl font-semibold tracking-wide text-gold">Temu Estates</a>

        <nav class="hidden items-center gap-6 text-sm lg:flex">
            @foreach ($nav as $route => $label)
                <a href="{{ route($route) }}" class="{{ request()->routeIs($route) ? 'text-gold' : 'text-neutral-300 hover:text-gold' }}">{{ $label }}</a>
            @endforeach
            <x-whatsapp-button label="Contact agent" class="px-4 py-2 text-sm" />
        </nav>

        <button @click="open = !open" class="rounded border border-white/20 px-3 py-2 text-sm text-gold lg:hidden" aria-label="Menu" :aria-expanded="open">
            <span x-text="open ? 'Close' : 'Menu'">Menu</span>
        </button>
    </div>
    <nav x-show="open" x-cloak class="border-t border-white/10 bg-black px-4 pb-4 lg:hidden">
        @foreach ($nav as $route => $label)
            <a href="{{ route($route) }}" class="block border-b border-white/5 py-3 {{ request()->routeIs($route) ? 'text-gold' : '' }}">{{ $label }}</a>
        @endforeach
    </nav>
</header>

@if (session('success'))
    <div class="bg-emerald-500/15 px-4 py-3 text-center text-emerald-300">{{ session('success') }}</div>
@endif

<main>{{ $slot }}</main>

<footer class="mt-20 border-t border-white/10 bg-neutral-950">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 md:grid-cols-3">
        <div>
            <p class="font-display text-2xl text-gold">Temu Estates</p>
            <p class="mt-2 text-sm">Building trust. Building futures.</p>
            <p class="mt-4 text-sm text-neutral-500">Property sales, rentals and consultancy in Addis Ababa, Ethiopia.</p>
        </div>
        <div class="text-sm">
            <p class="mb-3 font-semibold text-white">Explore</p>
            @foreach ($nav as $route => $label)
                <a href="{{ route($route) }}" class="block py-1 hover:text-gold">{{ $label }}</a>
            @endforeach
        </div>
        <div class="text-sm">
            <p class="mb-3 font-semibold text-white">Contact</p>
            @if ($phone)<a href="tel:{{ preg_replace('/\s/', '', $phone) }}" class="block py-1 hover:text-gold">{{ $phone }}</a>@endif
            @if ($email = \App\Models\Setting::get('email'))<a href="mailto:{{ $email }}" class="block py-1 hover:text-gold">{{ $email }}</a>@endif
            <p class="py-1">{{ \App\Models\Setting::get('address') }}</p>
            <x-whatsapp-button class="mt-3 px-4 py-2" />
        </div>
    </div>
    <p class="border-t border-white/10 py-4 text-center text-xs text-neutral-500">&copy; {{ date('Y') }} Temu Estates. All rights reserved.</p>
</footer>

{{-- Mobile bar: always one tap from WhatsApp or a call. Pages can replace it by passing a "bar" slot. --}}
<div class="fixed inset-x-0 bottom-0 z-40 border-t border-white/10 bg-black/95 p-3 backdrop-blur md:hidden"
     style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom))">
    @if (isset($bar))
        {{ $bar }}
    @else
        <div class="grid grid-cols-2 gap-3">
            <x-whatsapp-button class="justify-center py-3" />
            @if ($phone)<a href="tel:{{ preg_replace('/\s/', '', $phone) }}" class="rounded-lg border border-gold py-3 text-center font-semibold text-gold">Call</a>@endif
        </div>
    @endif
</div>

@livewireScripts
</body>
</html>
