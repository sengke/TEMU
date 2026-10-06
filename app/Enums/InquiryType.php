<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InquiryType: string implements HasLabel
{
    case Inquiry = 'inquiry';
    case Viewing = 'viewing';
    case General = 'general';

    public function label(): string
    {
        return match ($this) {
            self::Inquiry => 'Inquiry',
            self::Viewing => 'Viewing request',
            self::General => 'General message',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
