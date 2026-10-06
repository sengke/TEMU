<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Models\City;
use App\Models\Location;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Locations';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(80)->placeholder('e.g. Bole'),
            Select::make('city_id')->label('City')->options(fn () => City::pluck('name', 'id'))
                ->default(fn () => City::query()->value('id'))->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('city.name')->label('City'),
                TextColumn::make('properties_count')->label('Properties')->counts('properties'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (Location $record) => $record->properties()->withTrashed()->doesntExist())
                    ->tooltip('Only locations with no properties can be deleted'),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageLocations::route('/')];
    }
}
