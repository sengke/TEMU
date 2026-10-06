<?php

namespace App\Filament\Resources;

use App\Enums\ListingType;
use App\Enums\SubmissionStatus;
use App\Filament\Resources\PropertySubmissionResource\Pages;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertySubmission;
use App\Models\PropertyType;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PropertySubmissionResource extends Resource
{
    protected static ?string $model = PropertySubmission::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Property submissions';
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false; // submissions come from the website
    }

    public static function getNavigationBadge(): ?string
    {
        $count = PropertySubmission::where('status', SubmissionStatus::Submitted->value)->count();

        return $count ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')->options(SubmissionStatus::class)->required()
                ->helperText('Submitted, then Under review, then Approved, then Published (or Rejected).'),
            Select::make('listing_type')->label('Sale or rent')->options(ListingType::class)->required(),
            TextInput::make('name')->label('Owner name')->required(),
            TextInput::make('phone')->required(),
            TextInput::make('whatsapp'),
            TextInput::make('location')->required(),
            Select::make('property_type_id')->label('Property type')->relationship('propertyType', 'name'),
            TextInput::make('expected_price')->numeric()->prefix('ETB'),
            TextInput::make('bedrooms')->numeric(),
            TextInput::make('size_sqm')->label('Size (m²)')->numeric(),
            Textarea::make('description')->rows(4)->columnSpanFull(),
            Textarea::make('additional_info')->rows(3)->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('photos')->collection('photos')->multiple()->image()
                ->disabled()->openable()->downloadable()->columnSpanFull(),
            Textarea::make('admin_notes')->label('Internal notes')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('photos')->collection('photos')->limit(1)->label('Photo'),
                TextColumn::make('created_at')->label('Received')->dateTime('j M, H:i')->sortable(),
                TextColumn::make('name')->label('Owner')->searchable()->weight('bold')->description(fn (PropertySubmission $r) => $r->phone),
                TextColumn::make('location')->searchable(),
                TextColumn::make('propertyType.name')->label('Type')->toggleable(),
                TextColumn::make('listing_type')->label('Sale or rent')->badge(),
                TextColumn::make('expected_price')->label('Expected price')->numeric()->toggleable(),
                TextColumn::make('status')->badge(),
            ])
            ->filters([SelectFilter::make('status')->options(SubmissionStatus::class)])
            ->recordActions([
                Action::make('createListing')->label('Create listing')->icon('heroicon-o-plus-circle')->color('success')
                    ->visible(fn (PropertySubmission $record) => ! $record->property_id && $record->status !== SubmissionStatus::Rejected)
                    ->requiresConfirmation()
                    ->modalDescription("This creates an unpublished draft listing with the owner's details and photos. You can edit it before publishing.")
                    ->action(function (PropertySubmission $record) {
                        $text = mb_strtolower($record->location);
                        $location = Location::all()->first(fn ($l) => str_contains($text, mb_strtolower($l->name)))
                            ?? Location::query()->orderBy('id')->first();
                        $typeName = $record->propertyType?->name ?? 'Property';

                        $property = Property::create([
                            'title' => $typeName . ' for ' . ($record->listing_type === ListingType::Rent ? 'rent' : 'sale') . ' in ' . $record->location,
                            'description' => trim($record->description . "\n\n" . $record->additional_info),
                            'listing_type' => $record->listing_type,
                            'price' => $record->expected_price ?? 0,
                            'location_id' => $location->id,
                            'property_type_id' => $record->property_type_id ?? PropertyType::query()->value('id'),
                            'bedrooms' => $record->bedrooms,
                            'size_sqm' => $record->size_sqm,
                            'is_published' => false,
                        ]);

                        foreach ($record->getMedia('photos') as $media) {
                            $media->copy($property, 'gallery');
                        }

                        $record->update(['status' => SubmissionStatus::Approved, 'property_id' => $property->id]);

                        Notification::make()->success()->title('Draft listing created')
                            ->body('Open it, check the details, then switch on "Show on website".')
                            ->actions([
                                Action::make('open')->label('Open listing')->button()
                                    ->url(PropertyResource::getUrl('edit', ['record' => $property])),
                            ])->send();
                    }),
                Action::make('reject')->label('Reject')->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn (PropertySubmission $record) => $record->status !== SubmissionStatus::Rejected)
                    ->requiresConfirmation()
                    ->action(fn (PropertySubmission $record) => $record->update(['status' => SubmissionStatus::Rejected])),
                EditAction::make()->label('Open'),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPropertySubmissions::route('/'),
            'edit' => Pages\EditPropertySubmission::route('/{record}/edit'),
        ];
    }
}
