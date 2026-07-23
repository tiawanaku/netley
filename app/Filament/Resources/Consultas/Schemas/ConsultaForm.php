<?php

namespace App\Filament\Resources\Consultas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ConsultaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->email()
                    ->required(),
                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->tel()
                    ->required(),
                TextInput::make('ciudad'),
                TextInput::make('tipo_proceso')
                    ->label('Tipo de proceso'),
                Textarea::make('descripcion')
                    ->label('Descripción de la consulta')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('origen')
                    ->label('Origen')
                    ->options([
                        'landing' => 'Landing Page',
                        'telefono' => 'Teléfono',
                        'presencial' => 'Presencial',
                    ])
                    ->default('landing')
                    ->required(),
                Select::make('forma_ingreso')
                    ->label('Forma de ingreso')
                    ->options([
                        'Netley' => 'Netley',
                        'Social' => 'Social',
                        'Psicología' => 'Psicología',
                        'Taller' => 'Taller',
                        'Otros' => 'Otros',
                    ])
                    ->default('Netley')
                    ->required(),
                TextInput::make('colegio_u_otro')
                    ->label('Colegio / Otros'),
            ]);
    }
}
