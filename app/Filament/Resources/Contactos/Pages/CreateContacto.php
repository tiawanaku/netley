<?php

namespace App\Filament\Resources\Contactos\Pages;

use App\Filament\Resources\Contactos\ContactoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContacto extends CreateRecord
{
    protected static string $resource = ContactoResource::class;

    /**
     * RN CU-003: cada intento de contacto queda registrado bajo quien lo realizó (usuario autenticado).
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
