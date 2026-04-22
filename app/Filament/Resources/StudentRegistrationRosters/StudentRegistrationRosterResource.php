<?php

namespace App\Filament\Resources\StudentRegistrationRosters;

use App\Filament\Resources\StudentRegistrationRosters\Pages\ManageStudentRegistrationRosters;
use App\Models\StudentRegistrationRoster;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class StudentRegistrationRosterResource extends Resource
{
    protected static ?string $model = StudentRegistrationRoster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Academic Data';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Roster Entry')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('batch_token')
                                    ->required()
                                    ->maxLength(64),
                                TextInput::make('academic_year')
                                    ->required()
                                    ->maxLength(9),
                                Select::make('semester')
                                    ->options([
                                        'first' => 'First',
                                        'second' => 'Second',
                                    ])
                                    ->required(),
                                TextInput::make('student_id')
                                    ->required()
                                    ->maxLength(32),
                                TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('year_level')
                                    ->maxLength(16),
                                Select::make('imported_by')
                                    ->relationship('importer', 'name')
                                    ->searchable()
                                    ->preload(),
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
                TextColumn::make('student_id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year_level')
                    ->toggleable(),
                TextColumn::make('semester')
                    ->badge(),
                TextColumn::make('academic_year')
                    ->sortable(),
                TextColumn::make('importer.name')
                    ->label('Imported By')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('semester')
                    ->options([
                        'first' => 'First',
                        'second' => 'Second',
                    ]),
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
            'index' => ManageStudentRegistrationRosters::route('/'),
        ];
    }
}
