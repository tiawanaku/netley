<?php

namespace App\Filament\Resources\Casos;

use App\Filament\Resources\Casos\Pages\CreateCaso;
use App\Filament\Resources\Casos\Pages\EditCaso;
use App\Filament\Resources\Casos\Pages\ListCasos;
use App\Filament\Resources\Casos\Schemas\CasoForm;
use App\Filament\Resources\Casos\Tables\CasosTable;
use App\Models\Caso;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CasoResource extends Resource
{
    protected static ?string $model = Caso::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CasoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CasosTable::configure($table);
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
            'index' => ListCasos::route('/'),
            'create' => CreateCaso::route('/create'),
            'edit' => EditCaso::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
