<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::components());
    }

    public static function components(): array
    {
        return [
            TextInput::make('name')
                ->label('Nombre')
                ->required(),
            TextInput::make('email')
                ->label('Correo electrónico')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('password')
                ->label('Contraseña')
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->helperText('Déjalo en blanco para no cambiar la contraseña actual.'),
            Select::make('roles')
                ->label('Roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->searchable()
                ->preload()
                ->helperText('El rol (Abogado, Psicólogo, Pasante, etc.) determina qué puede ver y hacer este usuario en el panel.'),
        ];
    }
}
