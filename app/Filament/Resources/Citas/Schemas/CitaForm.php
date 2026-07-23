<?php

namespace App\Filament\Resources\Citas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CitaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->required(),
                Select::make('consulta_id')
                    ->label('Consulta relacionada')
                    ->relationship('consulta', 'nombre')
                    ->searchable()
                    ->default(null),
                DateTimePicker::make('fecha_hora')
                    ->label('Fecha y hora')
                    ->required(),
                Select::make('modalidad')
                    ->options(['Presencial' => 'Presencial', 'Virtual' => 'Virtual'])
                    ->required(),
                Select::make('estado')
                    ->options([
            'Pendiente' => 'Pendiente',
            'Confirmada' => 'Confirmada',
            'Cancelada' => 'Cancelada',
            'Reagendada' => 'Reagendada',
            'Realizada' => 'Realizada',
        ])
                    ->default('Pendiente')
                    ->required(),
                Textarea::make('notas')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
