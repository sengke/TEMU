<?php

namespace App\Filament\Resources;

use App\Enums\InquiryType;
use App\Filament\Resources\InquiryResource\Pages;
use App\Models\Inquiry;
use App\Support\Whatsapp;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationLabel = 'Leads and inquiries';
    protected static ?string $modelLabel = 'inquiry';
    protected static ?string $pluralModelLabel = 'inquiries';
    protected static ?int $navigationSort = 2;

    public const STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'];

    public static function canCreate(): bool
    {
        return false; // leads come from the website
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Inquiry::where('status', 'new')->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')->options(self::STATUSES)->required(),
            Select::make('type')->options(InquiryType::class)->disabled(),
            Select::make('property_id')->label('Property')->relationship('property', 'title')->disabled(),
            TextInput::make('name')->required(),
            TextInput::make('phone')->required(),
            TextInput::make('whatsapp'),
            TextInput::make('email')->email(),
            DatePicker::make('preferred_date'),
            Textarea::make('message')->rows(5)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Received')->dateTime('j M, H:i')->sortable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('phone')->searchable()->copyable(),
                TextColumn::make('whatsapp')->copyable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('property.title')->label('Property')->limit(30)->placeholder('General'),
                TextColumn::make('message')->limit(50)->toggleable(),
                SelectColumn::make('status')->options(self::STATUSES)->selectablePlaceholder(false),
            ])
            ->filters([
                SelectFilter::make('status')->options(self::STATUSES),
                SelectFilter::make('type')->options(InquiryType::class),
            ])
            ->recordActions([
                Action::make('whatsapp')->label('WhatsApp')->icon('heroicon-o-chat-bubble-left-right')->color('success')
                    ->url(fn (Inquiry $record) => 'https://wa.me/' . Whatsapp::international($record->whatsapp ?: $record->phone), shouldOpenInNewTab: true),
                Action::make('call')->label('Call')->icon('heroicon-o-phone')
                    ->url(fn (Inquiry $record) => 'tel:' . preg_replace('/\s/', '', $record->phone)),
                EditAction::make()->label('Open'),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInquiries::route('/'),
            'edit' => Pages\EditInquiry::route('/{record}/edit'),
        ];
    }
}
