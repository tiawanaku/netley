<?php

namespace App\Filament\Resources\Casos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CasoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // CU-008 Asignar Caso + CU-009 Crear Caso Jurídico
                Section::make('Asignación del caso')
                    ->columns(3)
                    ->schema([
                        Placeholder::make('codigo')
                            ->label('Código')
                            ->content(fn (?\App\Models\Caso $record) => $record?->codigo ?? 'Se genera automáticamente al crear')
                            ->visibleOn('edit'),
                        Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'nombres')
                            ->searchable()
                            ->required(),
                        Select::make('abogado_id')
                            ->label('Abogado')
                            ->relationship('abogado', 'name')
                            ->searchable(),
                        Select::make('procurador_id')
                            ->label('Procurador')
                            ->relationship('procurador', 'name')
                            ->searchable(),
                        TextInput::make('especialidad')
                            ->required(),
                        TextInput::make('tipo')
                            ->label('Tipo de proceso'),
                        TextInput::make('juzgado'),
                        Select::make('estado')
                            ->options([
                                'Abierto' => 'Abierto',
                                'En Proceso' => 'En proceso',
                                'Suspendido' => 'Suspendido',
                                'Cerrado' => 'Cerrado',
                            ])
                            ->default('Abierto')
                            ->required(),
                        Select::make('prioridad')
                            ->options(['Baja' => 'Baja', 'Normal' => 'Normal', 'Alta' => 'Alta', 'Urgente' => 'Urgente'])
                            ->default('Normal')
                            ->required(),
                        DatePicker::make('fecha_inicio')
                            ->default(now())
                            ->required(),
                        DatePicker::make('fecha_fin'),
                    ]),
                // CU-010 Completar Ficha del Caso (se llena después de asignado el caso)
                Section::make('Ficha del caso')
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        TextInput::make('juez'),
                        TextInput::make('secretario'),
                        TextInput::make('fiscal'),
                        TextInput::make('demandante'),
                        TextInput::make('demandado'),
                        TextInput::make('numero_expediente')
                            ->label('N.º de expediente'),
                        TextInput::make('nurej')
                            ->label('NUREJ'),
                        TextInput::make('delito'),
                        TextInput::make('materia'),
                        TextInput::make('tribunal'),
                        TextInput::make('telefonos')
                            ->label('Teléfonos'),
                        TextInput::make('correos')
                            ->label('Correos'),
                        TextInput::make('direccion')
                            ->label('Dirección'),
                        Textarea::make('observaciones')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
