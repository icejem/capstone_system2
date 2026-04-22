<?php

namespace App\Filament\Resources\LoginVerifications;

use App\Filament\Resources\LoginVerifications\Pages\ManageLoginVerifications;
use App\Models\LoginVerification;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
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

class LoginVerificationResource extends Resource
{
    protected static ?string $model = LoginVerification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Security & Audit';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Login Verification')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('token_hash')
                                    ->required()
                                    ->maxLength(64),
                                Toggle::make('remember'),
                                TextInput::make('device_label')
                                    ->maxLength(255),
                                TextInput::make('device_fingerprint_hash')
                                    ->maxLength(64),
                                Select::make('trusted_device_id')
                                    ->relationship('trustedDevice', 'device_label')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('ip_address')
                                    ->maxLength(45),
                                TextInput::make('denied_reason')
                                    ->maxLength(255),
                                DateTimePicker::make('sent_at')
                                    ->required(),
                                DateTimePicker::make('expires_at')
                                    ->required(),
                                DateTimePicker::make('last_resent_at'),
                                DateTimePicker::make('verified_at'),
                                DateTimePicker::make('denied_at'),
                                DateTimePicker::make('consumed_at'),
                                DateTimePicker::make('invalidated_at'),
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
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('remember')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Remembered' : 'One-time'),
                TextColumn::make('device_label')
                    ->toggleable(),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('denied_at')
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
            ->defaultSort('sent_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLoginVerifications::route('/'),
        ];
    }
}
