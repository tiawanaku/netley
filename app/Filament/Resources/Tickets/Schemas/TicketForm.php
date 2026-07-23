<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('consulta_id')
                    ->relationship('consulta', 'nombre')
                    ->required()
                    ->disabled(),
                Select::make('asignado_a')
                    ->label('Asignado a')
                    ->relationship('asignadoA', 'name'),
                Select::make('estado')
                    ->options([
            'Pendiente' => 'Pendiente',
            'Asignado' => 'Asignado',
            'En Atención' => 'En atención',
            'Cerrado' => 'Cerrado',
        ])
                    ->default('Pendiente')
                    ->required(),
                Select::make('prioridad')
                    ->options(['Baja' => 'Baja', 'Normal' => 'Normal', 'Alta' => 'Alta', 'Urgente' => 'Urgente'])
                    ->default('Normal')
                    ->required(),
                DateTimePicker::make('fecha_asignacion'),
            ]);
    }
}
