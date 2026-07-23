<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use App\Filament\Resources\Clientes\Concerns\GeneratesStaffAccess;
use Filament\Resources\Pages\CreateRecord;

class CreateCliente extends CreateRecord
{
    use GeneratesStaffAccess;

    protected static string $resource = ClienteResource::class;

    protected function afterCreate(): void
    {
        $this->generarAccesoStaffSiCorresponde();
    }
}
