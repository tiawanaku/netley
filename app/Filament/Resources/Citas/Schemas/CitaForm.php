<?php

namespace App\Filament\Resources\Citas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CitaForm
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
                // CU-005 Registrar Resultado de la Cita (lo llena el abogado tras la reunión)
                Section::make('Resultado de la cita')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Textarea::make('diagnostico_preliminar')
                            ->label('Diagnóstico preliminar')
                            ->columnSpanFull(),
                        TextInput::make('tipo_servicio')
                            ->label('Tipo de servicio requerido'),
                        TextInput::make('costo')
                            ->label('Costo (Bs.)')
                            ->numeric(),
                        Select::make('riesgo')
                            ->options(['Bajo' => 'Bajo', 'Medio' => 'Medio', 'Alto' => 'Alto']),
                        Select::make('deriva_a')
                            ->label('Deriva a')
                            ->options([
                                'Legal' => 'Legal',
                                'Psicología' => 'Psicología',
                                'Conciliación' => 'Conciliación',
                                'Otro' => 'Otro',
                            ]),
                        Textarea::make('recomendaciones')
                            ->columnSpanFull(),
                        Textarea::make('observaciones')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
