<?php

namespace App\Filament\Resources\LoginVerifications\Pages;

use App\Filament\Resources\LoginVerifications\LoginVerificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLoginVerifications extends ManageRecords
{
    protected static string $resource = LoginVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
