<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ListingType: string implements HasLabel
{
    case Sale = 'sale';
    case Rent = 'rent';

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'For sale',
            self::Rent => 'For rent',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
