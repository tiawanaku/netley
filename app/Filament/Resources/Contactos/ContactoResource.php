<?php

namespace App\Filament\Resources\Contactos;

use App\Filament\Resources\Contactos\Pages\CreateContacto;
use App\Filament\Resources\Contactos\Pages\ListContactos;
use App\Filament\Resources\Contactos\Schemas\ContactoForm;
use App\Filament\Resources\Contactos\Tables\ContactosTable;
use App\Models\Contacto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContactoResource extends Resource
{
    protected static ?string $model = Contacto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function form(Schema $schema): Schema
    {
        return ContactoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactos::route('/'),
            'create' => CreateContacto::route('/create'),
        ];
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
