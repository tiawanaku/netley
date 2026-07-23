<?php

namespace App\Filament\Resources\Clientes\Schemas;

use App\Models\Cliente;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos personales')
                    ->columns(3)
                    ->schema([
                        TextInput::make('nombres')
                            ->required(),
                        TextInput::make('apellido_paterno')
                            ->label('Apellido paterno'),
                        TextInput::make('apellido_materno')
                            ->label('Apellido materno'),
                        TextInput::make('ci')
                            ->label('C.I.'),
                        Select::make('genero')
                            ->label('Género')
                            ->options([
                                'Femenino' => 'Femenino',
                                'Masculino' => 'Masculino',
                                'Otro' => 'Otro',
                            ]),
                        DatePicker::make('fecha_nacimiento')
                            ->label('Fecha de nacimiento'),
                        TextInput::make('nacionalidad'),
                        Select::make('estado_civil')
                            ->label('Estado civil')
                            ->options([
                                'Soltero/a' => 'Soltero/a',
                                'Casado/a' => 'Casado/a',
                                'Divorciado/a' => 'Divorciado/a',
                                'Viudo/a' => 'Viudo/a',
                                'Concubino/a' => 'Concubino/a',
                            ]),
                        TextInput::make('profesion')
                            ->label('Profesión'),
                    ]),
                Section::make('Contacto y ubicación')
                    ->columns(3)
                    ->schema([
                        TextInput::make('telefono')
                            ->tel(),
                        TextInput::make('whatsapp')
                            ->tel(),
                        TextInput::make('correo')
                            ->label('Correo electrónico')
                            ->email()
                            ->required(),
                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->columnSpan(2),
                        TextInput::make('ciudad'),
                    ]),
                Section::make('Datos de Netley')
                    ->columns(3)
                    ->schema([
                        TextInput::make('numero_contrato')
                            ->label('N.º de contrato'),
                        Select::make('estado')
                            ->options([
                                'Activo' => 'Activo',
                                'Inactivo' => 'Inactivo',
                                'Suspendido' => 'Suspendido',
                            ])
                            ->default('Activo')
                            ->required(),
                        DatePicker::make('fecha_de_inicio')
                            ->label('Fecha de inicio'),
                        Toggle::make('es_preferente')
                            ->label('Cliente ejecutivo')
                            ->helperText('Se activa automáticamente al registrar el primer pago (CU-006).')
                            ->disabled()
                            ->columnSpanFull(),
                        Select::make('rol_empresa')
                            ->label('Rol en la empresa')
                            ->placeholder('Ninguno (es solo cliente)')
                            ->options([
                                'Abogado' => 'Abogado',
                                'Psicólogo' => 'Psicólogo',
                                'Procurador' => 'Procurador',
                                'Trabajo Social' => 'Trabajo Social',
                                'Pasante' => 'Pasante',
                                'Otros' => 'Otros',
                            ])
                            ->helperText(fn (?Cliente $record) => $record?->user_id
                                ? 'Ya tiene usuario para entrar al panel.'
                                : 'Al guardar con un rol seleccionado se genera automáticamente su usuario y contraseña.')
                            ->columnSpanFull(),
                        FileUpload::make('foto')
                            ->image()
                            ->columnSpanFull(),
                        Textarea::make('nota_netley')
                            ->label('Nota Netley')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
