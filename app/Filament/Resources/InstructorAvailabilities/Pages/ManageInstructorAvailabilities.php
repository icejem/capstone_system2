<?php

namespace App\Filament\Resources\InstructorAvailabilities\Pages;

use App\Filament\Resources\InstructorAvailabilities\InstructorAvailabilityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageInstructorAvailabilities extends ManageRecords
{
    protected static string $resource = InstructorAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
