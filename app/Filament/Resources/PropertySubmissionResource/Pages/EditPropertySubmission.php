<?php

namespace App\Filament\Resources\PropertySubmissionResource\Pages;

use App\Filament\Resources\PropertySubmissionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPropertySubmission extends EditRecord
{
    protected static string $resource = PropertySubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
