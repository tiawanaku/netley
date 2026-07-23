<?php

namespace App\Filament\Resources\Consultas\Pages;

use App\Filament\Resources\Consultas\ConsultaResource;
use App\Models\Cliente;
use Filament\Resources\Pages\CreateRecord;

class CreateConsulta extends CreateRecord
{
    protected static string $resource = ConsultaResource::class;

    /**
     * El visitante no conoce el concepto "Cliente": solo entrega nombre/email/telefono.
     * El sistema busca o crea el Cliente automáticamente (RN implícita del flujo CU-001).
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $cliente = Cliente::firstOrCreate(
            ['email' => $data['email']],
            ['nombre' => $data['nombre'], 'telefono' => $data['telefono']],
        );

        $data['cliente_id'] = $cliente->id;

        return $data;
    }
}
