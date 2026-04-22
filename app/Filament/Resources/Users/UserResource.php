<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Core Data';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('first_name')
                                    ->maxLength(255),
                                TextInput::make('last_name')
                                    ->maxLength(255),
                                TextInput::make('middle_name')
                                    ->maxLength(255),
                                TextInput::make('phone_number')
                                    ->tel()
                                    ->maxLength(20),
                                TextInput::make('student_id')
                                    ->maxLength(255),
                                Select::make('year_level')
                                    ->options(User::yearLevelLabels()),
                                Select::make('user_type')
                                    ->options([
                                        'student' => 'Student',
                                        'instructor' => 'Instructor',
                                        'admin' => 'Admin',
                                    ])
                                    ->required(),
                                Select::make('account_status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'suspended' => 'Suspended',
                                    ])
                                    ->required(),
                                DateTimePicker::make('email_verified_at'),
                                TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->minLength(8),
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('user_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('account_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('student_id')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('year_level')
                    ->label('Year')
                    ->toggleable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_type')
                    ->options([
                        'student' => 'Student',
                        'instructor' => 'Instructor',
                        'admin' => 'Admin',
                    ]),
                SelectFilter::make('account_status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
                TernaryFilter::make('email_verified_at')
                    ->label('Email verified'),
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
            'index' => ManageUsers::route('/'),
        ];
    }
}
