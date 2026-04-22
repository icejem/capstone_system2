<?php

namespace App\Filament\Resources\TrustedDevices\Pages;

use App\Filament\Resources\TrustedDevices\TrustedDeviceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTrustedDevices extends ManageRecords
{
    protected static string $resource = TrustedDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
