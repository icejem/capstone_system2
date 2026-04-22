<?php

namespace App\Filament\Resources\InstructorAvailabilities;

use App\Filament\Resources\InstructorAvailabilities\Pages\ManageInstructorAvailabilities;
use App\Models\InstructorAvailability;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
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

class InstructorAvailabilityResource extends Resource
{
    protected static ?string $model = InstructorAvailability::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Core Data';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Availability')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('instructor_id')
                                    ->relationship('instructor', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('semester')
                                    ->options([
                                        'first' => 'First',
                                        'second' => 'Second',
                                    ]),
                                Select::make('available_day')
                                    ->options([
                                        'Monday' => 'Monday',
                                        'Tuesday' => 'Tuesday',
                                        'Wednesday' => 'Wednesday',
                                        'Thursday' => 'Thursday',
                                        'Friday' => 'Friday',
                                        'Saturday' => 'Saturday',
                                        'Sunday' => 'Sunday',
                                    ]),
                                TextInput::make('academic_year')
                                    ->maxLength(15),
                                DatePicker::make('available_date'),
                                Toggle::make('is_active')
                                    ->default(true),
                                TimePicker::make('start_time')
                                    ->seconds(false)
                                    ->required(),
                                TimePicker::make('end_time')
                                    ->seconds(false)
                                    ->required(),
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
                TextColumn::make('instructor.name')
                    ->label('Instructor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('semester')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('academic_year')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('available_day')
                    ->toggleable(),
                TextColumn::make('available_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->time('g:i A'),
                TextColumn::make('end_time')
                    ->time('g:i A'),
                TextColumn::make('is_active')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive'),
            ])
            ->filters([
                SelectFilter::make('semester')
                    ->options([
                        'first' => 'First',
                        'second' => 'Second',
                    ]),
                TernaryFilter::make('is_active'),
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
            ->defaultSort('available_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInstructorAvailabilities::route('/'),
        ];
    }
}
