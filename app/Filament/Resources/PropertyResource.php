<?php

namespace App\Filament\Resources;

use App\Enums\Furnished;
use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use App\Filament\Resources\PropertyResource\Pages;
use App\Models\City;
use App\Models\Property;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;
    protected static ?string $recordTitleAttribute = 'title';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationLabel = 'Properties';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Basic information')->columns(2)->columnSpanFull()->schema([
                TextInput::make('title')->required()->maxLength(160)->columnSpanFull()
                    ->placeholder('e.g. Luxury 3 Bedroom Apartment'),
                Select::make('listing_type')->label('For sale or rent')->options(ListingType::class)->required()->default('sale'),
                Select::make('status')->options(PropertyStatus::class)->required()->default('for_sale')
                    ->helperText('Use Sold, Rented or Reserved when the property is no longer available. It stays on the site with that label.'),
                TextInput::make('price')->numeric()->required()->minValue(0)->prefix('ETB')
                    ->helperText('For rent, enter the monthly rent.'),
                TextInput::make('currency')->required()->default('ETB')->maxLength(3),
                Select::make('location_id')->label('Location')->relationship('location', 'name')->required()->searchable()->preload()
                    ->createOptionForm([
                        TextInput::make('name')->label('New location name')->required(),
                        Select::make('city_id')->label('City')->options(fn () => City::pluck('name', 'id'))
                            ->default(fn () => City::query()->value('id'))->required(),
                    ]),
                Select::make('property_type_id')->label('Property type')->relationship('propertyType', 'name')->required()->searchable()->preload(),
            ]),

            Section::make('Details')->columns(3)->columnSpanFull()->schema([
                TextInput::make('bedrooms')->numeric()->minValue(0)->maxValue(50),
                TextInput::make('bathrooms')->numeric()->minValue(0)->maxValue(50),
                TextInput::make('size_sqm')->label('Size (m²)')->numeric()->minValue(0),
                TextInput::make('floor')->placeholder('e.g. 7th'),
                Select::make('furnished')->options(Furnished::class),
                Select::make('completion_status')->options([
                    'Completed' => 'Completed',
                    'Under construction' => 'Under construction',
                    'Off-plan' => 'Off-plan',
                ]),
                DatePicker::make('available_from'),
                Toggle::make('has_parking')->label('Parking available')->inline(false),
            ]),

            Section::make('Description')->columnSpanFull()->schema([
                Textarea::make('description')->rows(7)->columnSpanFull()
                    ->helperText('Plain text. A blank line starts a new paragraph.'),
            ]),

            Section::make('Photos and media')->columnSpanFull()->schema([
                SpatieMediaLibraryFileUpload::make('gallery')->label('Photos')->collection('gallery')
                    ->multiple()->reorderable()->image()->maxFiles(30)->maxSize(10240)->panelLayout('grid')
                    ->helperText('The first photo is the cover image. Drag to reorder.'),
                SpatieMediaLibraryFileUpload::make('floorplan')->label('Floor plan')->collection('floorplan')->image()->maxSize(10240),
                TextInput::make('video_url')->label('Video link (YouTube)')->url()
                    ->helperText('Recommended: upload the video to YouTube and paste the link here. It loads faster than a file.'),
                SpatieMediaLibraryFileUpload::make('video')->label('Or upload a short video')->collection('video')
                    ->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(12288),
            ]),

            Section::make('Amenities')->columnSpanFull()->schema([
                CheckboxList::make('amenities')->relationship('amenities', 'name')->columns(3)->bulkToggleable()
                    ->helperText('Add or remove amenities from the Amenities menu.'),
            ]),

            Section::make('Map location (optional)')->columns(2)->columnSpanFull()->collapsed()->schema([
                TextInput::make('latitude')->numeric()->placeholder('8.9960'),
                TextInput::make('longitude')->numeric()->placeholder('38.7870'),
                Textarea::make('map_help')->label('How to find these numbers')->disabled()->dehydrated(false)->rows(2)->columnSpanFull()
                    ->default('On Google Maps, right-click the property. The first line shows two numbers: the first is latitude, the second is longitude. Click them to copy.'),
            ]),

            Section::make('Publishing')->columns(3)->columnSpanFull()->schema([
                Toggle::make('is_published')->label('Show on website')->default(true)->inline(false),
                Toggle::make('is_featured')->label('Featured on homepage')->inline(false),
                Select::make('agent_id')->label('Agent')->relationship('agent', 'name')->searchable()->preload(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('gallery')->label('Photo')->collection('gallery')->conversion('thumb')->limit(1),
                TextColumn::make('title')->searchable()->sortable()->limit(40)
                    ->description(fn (Property $record) => $record->location?->name . ' - ' . $record->propertyType?->name),
                TextColumn::make('listing_type')->label('Type')->badge()->toggleable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('price')->label('Price (ETB)')->numeric()->sortable(),
                TextColumn::make('location.name')->label('Location')->searchable()->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_published')->label('On website'),
                ToggleColumn::make('is_featured')->label('Featured')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Added')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options(PropertyStatus::class),
                SelectFilter::make('listing_type')->label('Sale or rent')->options(ListingType::class),
                SelectFilter::make('location_id')->label('Location')->relationship('location', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    Action::make('markSold')->label('Mark as sold')->icon('heroicon-o-check-badge')->color('danger')
                        ->visible(fn (Property $record) => $record->listing_type === ListingType::Sale && $record->status !== PropertyStatus::Sold)
                        ->requiresConfirmation()
                        ->action(fn (Property $record) => $record->update(['status' => PropertyStatus::Sold])),
                    Action::make('markRented')->label('Mark as rented')->icon('heroicon-o-key')->color('danger')
                        ->visible(fn (Property $record) => $record->listing_type === ListingType::Rent && $record->status !== PropertyStatus::Rented)
                        ->requiresConfirmation()
                        ->action(fn (Property $record) => $record->update(['status' => PropertyStatus::Rented])),
                    Action::make('markReserved')->label('Mark as reserved')->icon('heroicon-o-clock')->color('info')
                        ->visible(fn (Property $record) => $record->status !== PropertyStatus::Reserved)
                        ->action(fn (Property $record) => $record->update(['status' => PropertyStatus::Reserved])),
                    Action::make('markAvailable')->label('Mark as available again')->icon('heroicon-o-arrow-path')->color('success')
                        ->visible(fn (Property $record) => ! $record->status->isAvailable())
                        ->action(fn (Property $record) => $record->update([
                            'status' => $record->listing_type === ListingType::Rent ? PropertyStatus::ForRent : PropertyStatus::ForSale,
                        ])),
                ])->label('Status')->icon('heroicon-o-tag')->button(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
