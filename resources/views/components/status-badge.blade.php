@props(['status'])
<span {{ $attributes->class(['inline-block rounded px-2.5 py-1 text-xs font-semibold', $status->badgeClasses()]) }}>{{ $status->label() }}</span>
