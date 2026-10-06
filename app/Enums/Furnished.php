<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Furnished: string implements HasLabel
{
    case Furnished = 'furnished';
    case Semi = 'semi_furnished';
    case Unfurnished = 'unfurnished';

    public function label(): string
    {
        return match ($this) {
            self::Furnished => 'Furnished',
            self::Semi => 'Semi-furnished',
            self::Unfurnished => 'Unfurnished',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
