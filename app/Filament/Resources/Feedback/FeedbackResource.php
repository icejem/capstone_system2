<?php

namespace App\Filament\Resources\Feedback;

use App\Filament\Resources\Feedback\Pages\ManageFeedback;
use App\Models\Feedback;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Core Data';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Feedback')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('consultation_id')
                                    ->relationship('consultation', 'id')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('student_id')
                                    ->relationship('student', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('instructor_id')
                                    ->relationship('instructor', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('rating')
                                    ->options([
                                        '1' => '1',
                                        '2' => '2',
                                        '3' => '3',
                                        '4' => '4',
                                        '5' => '5',
                                    ])
                                    ->required(),
                                Textarea::make('comments')
                                    ->rows(4)
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
                TextColumn::make('consultation_id')
                    ->label('Consultation')
                    ->sortable(),
                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('instructor.name')
                    ->label('Instructor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rating')
                    ->badge()
                    ->sortable(),
                TextColumn::make('comments')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('rating')
                    ->options([
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
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
            'index' => ManageFeedback::route('/'),
        ];
    }
}
