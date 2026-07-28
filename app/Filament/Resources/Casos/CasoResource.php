<?php

namespace App\Filament\Resources\Casos;

use App\Filament\Resources\Casos\Pages\CreateCaso;
use App\Filament\Resources\Casos\Pages\EditCaso;
use App\Filament\Resources\Casos\Pages\ListCasos;
use App\Filament\Resources\Casos\RelationManagers\ActuacionesRelationManager;
use App\Filament\Resources\Casos\RelationManagers\AudienciasRelationManager;
use App\Filament\Resources\Casos\RelationManagers\ConciliacionesRelationManager;
use App\Filament\Resources\Casos\RelationManagers\DiligenciasRelationManager;
use App\Filament\Resources\Casos\RelationManagers\DocumentosRelationManager;
use App\Filament\Resources\Casos\RelationManagers\EquipoRelationManager;
use App\Filament\Resources\Casos\RelationManagers\HitosRelationManager;
use App\Filament\Resources\Casos\RelationManagers\MedidasCautelaresRelationManager;
use App\Filament\Resources\Casos\RelationManagers\ObservacionesRelationManager;
use App\Filament\Resources\Casos\RelationManagers\ResolucionesJudicialesRelationManager;
use App\Filament\Resources\Casos\RelationManagers\SolicitudesDocumentoRelationManager;
use App\Filament\Resources\Casos\RelationManagers\WorkflowEtapasRelationManager;
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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

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
            EquipoRelationManager::class,
            WorkflowEtapasRelationManager::class,
            ActuacionesRelationManager::class,
            HitosRelationManager::class,
            AudienciasRelationManager::class,
            ConciliacionesRelationManager::class,
            DiligenciasRelationManager::class,
            MedidasCautelaresRelationManager::class,
            ResolucionesJudicialesRelationManager::class,
            DocumentosRelationManager::class,
            SolicitudesDocumentoRelationManager::class,
            ObservacionesRelationManager::class,
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
