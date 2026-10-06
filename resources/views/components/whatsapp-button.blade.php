@props(['message' => null, 'label' => 'WhatsApp'])
<a href="{{ \App\Support\Whatsapp::url($message ?? 'Hello Temu Estates, I would like some help with property.') }}"
   target="_blank" rel="noopener"
   {{ $attributes->class(['inline-flex items-center gap-2 rounded-lg bg-gold font-semibold text-black transition hover:bg-gold-light']) }}>
    {{ $label }}
</a>
