<?php

namespace App\Filament\Resources\StudentRegistrationRosters\Pages;

use App\Filament\Resources\StudentRegistrationRosters\StudentRegistrationRosterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStudentRegistrationRosters extends ManageRecords
{
    protected static string $resource = StudentRegistrationRosterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
