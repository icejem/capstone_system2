<?php

namespace App\Filament\Resources\UserNotifications;

use App\Filament\Resources\UserNotifications\Pages\ManageUserNotifications;
use App\Models\UserNotification;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class UserNotificationResource extends Resource
{
    protected static ?string $model = UserNotification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Messaging';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Notification')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('type')
                                    ->maxLength(255),
                                Toggle::make('is_read'),
                                Textarea::make('message')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('is_read')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Read' : 'Unread'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_read'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUserNotifications::route('/'),
        ];
    }
}
