<?php

namespace App\Filament\Resources\TrustedDevices;

use App\Filament\Resources\TrustedDevices\Pages\ManageTrustedDevices;
use App\Models\TrustedDevice;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class TrustedDeviceResource extends Resource
{
    protected static ?string $model = TrustedDevice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Security & Audit';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Trusted Device')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('fingerprint_hash')
                                    ->required()
                                    ->maxLength(64),
                                TextInput::make('device_label')
                                    ->maxLength(255),
                                TextInput::make('device_type')
                                    ->maxLength(255),
                                TextInput::make('browser')
                                    ->maxLength(255),
                                TextInput::make('operating_system')
                                    ->maxLength(255),
                                TextInput::make('ip_address')
                                    ->maxLength(45),
                                TextInput::make('location')
                                    ->maxLength(255),
                                DateTimePicker::make('trusted_at'),
                                DateTimePicker::make('last_used_at'),
                                DateTimePicker::make('revoked_at'),
                                Textarea::make('user_agent')
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
                TextColumn::make('device_label')
                    ->searchable(),
                TextColumn::make('device_type')
                    ->toggleable(),
                TextColumn::make('browser')
                    ->toggleable(),
                TextColumn::make('trusted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_used_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('revoked_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
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
            ->defaultSort('trusted_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTrustedDevices::route('/'),
        ];
    }
}
