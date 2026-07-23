<?php

namespace App\Filament\Resources\Pagos\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PagoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombres')
                    ->searchable()
                    ->required(),
                Select::make('consulta_id')
                    ->label('Consulta relacionada')
                    ->relationship('consulta', 'nombre')
                    ->searchable()
                    ->default(null),
                TextInput::make('numero_recibo')
                    ->label('N.º de recibo')
                    ->default(fn () => 'REC-'.now()->format('Ymd-His'))
                    ->required(),
                TextInput::make('monto')
                    ->label('Monto (Bs.)')
                    ->required()
                    ->numeric()
                    ->minValue(0.01),
                DateTimePicker::make('fecha_pago')
                    ->label('Fecha de pago')
                    ->default(now())
                    ->required(),
            ]);
    }
}
