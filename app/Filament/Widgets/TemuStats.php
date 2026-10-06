<?php

namespace App\Filament\Widgets;

use App\Enums\SubmissionStatus;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PropertySubmission;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TemuStats extends StatsOverviewWidget
{
    protected static ?int $sort = -10;

    protected function getStats(): array
    {
        return [
            Stat::make('Live properties', Property::where('is_published', true)->count())
                ->description(Property::where('status', 'sold')->count() . ' sold, ' . Property::where('status', 'rented')->count() . ' rented'),
            Stat::make('New leads', Inquiry::where('status', 'new')->count())
                ->description(Inquiry::count() . ' inquiries in total')->color('danger'),
            Stat::make('Submissions to review', PropertySubmission::where('status', SubmissionStatus::Submitted->value)->count())
                ->description('From the List Your Property page')->color('warning'),
        ];
    }
}
