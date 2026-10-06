<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum PropertyStatus: string implements HasLabel, HasColor
{
    case ForSale = 'for_sale';
    case ForRent = 'for_rent';
    case Sold = 'sold';
    case Rented = 'rented';
    case Reserved = 'reserved';

    public function label(): string
    {
        return match ($this) {
            self::ForSale => 'For sale',
            self::ForRent => 'For rent',
            self::Sold => 'Sold',
            self::Rented => 'Rented',
            self::Reserved => 'Reserved',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::ForSale => 'bg-gold text-black',
            self::ForRent => 'bg-emerald-400 text-black',
            self::Sold, self::Rented => 'bg-red-600 text-white',
            self::Reserved => 'bg-amber-300 text-black',
        };
    }

    public function isAvailable(): bool
    {
        return in_array($this, [self::ForSale, self::ForRent], true);
    }

    /** Handy for admin dropdowns: ['for_sale' => 'For sale', ...] */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all();
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ForSale => 'warning',
            self::ForRent => 'success',
            self::Sold, self::Rented => 'danger',
            self::Reserved => 'info',
        };
    }
}
