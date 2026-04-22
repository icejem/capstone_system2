<?php

namespace App\Filament\Resources\Consultations;

use App\Filament\Resources\Consultations\Pages\ManageConsultations;
use App\Models\Consultation;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class ConsultationResource extends Resource
{
    protected static ?string $model = Consultation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Core Data';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Consultation')
                    ->schema([
                        Grid::make(2)
                            ->schema([
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
                                DatePicker::make('consultation_date')
                                    ->required(),
                                TimePicker::make('consultation_time')
                                    ->seconds(false)
                                    ->required(),
                                TimePicker::make('consultation_end_time')
                                    ->seconds(false),
                                Select::make('consultation_mode')
                                    ->options([
                                        'online' => 'Online',
                                        'face_to_face' => 'Face to Face',
                                        'hybrid' => 'Hybrid',
                                    ])
                                    ->searchable(),
                                TextInput::make('consultation_type')
                                    ->maxLength(255)
                                    ->required(),
                                TextInput::make('consultation_category')
                                    ->maxLength(255),
                                TextInput::make('consultation_topic')
                                    ->maxLength(255),
                                Select::make('consultation_priority')
                                    ->options([
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High',
                                        'urgent' => 'Urgent',
                                    ]),
                                Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'in_progress' => 'In Progress',
                                        'completed' => 'Completed',
                                        'incompleted' => 'Incompleted',
                                        'declined' => 'Declined',
                                    ])
                                    ->required(),
                                TextInput::make('duration_minutes')
                                    ->numeric()
                                    ->minValue(0),
                                TextInput::make('call_attempts')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),
                                Toggle::make('transcript_active'),
                                DateTimePicker::make('started_at'),
                                DateTimePicker::make('ended_at'),
                                DateTimePicker::make('reminder_sent_at'),
                                DateTimePicker::make('reminder_30_sent_at'),
                                DateTimePicker::make('reminder_10_sent_at'),
                                Textarea::make('student_notes')
                                    ->columnSpanFull(),
                                Textarea::make('summary_text')
                                    ->columnSpanFull(),
                                Textarea::make('transcript_text')
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
                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('instructor.name')
                    ->label('Instructor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type_label')
                    ->label('Type')
                    ->searchable(),
                TextColumn::make('consultation_mode')
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('consultation_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('consultation_time')
                    ->time('g:i A')
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->numeric()
                    ->suffix(' min')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'incompleted' => 'Incompleted',
                        'declined' => 'Declined',
                    ]),
                SelectFilter::make('consultation_mode')
                    ->options([
                        'online' => 'Online',
                        'face_to_face' => 'Face to Face',
                        'hybrid' => 'Hybrid',
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
            ->defaultSort('consultation_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageConsultations::route('/'),
        ];
    }
}
