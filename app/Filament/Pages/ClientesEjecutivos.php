<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Clientes\Actions\VerExpedienteClienteAction;
use App\Models\Cliente;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Bandeja personal del profesional (abogado, psicólogo, etc.): Clientes que ya pagaron
 * (Cliente::es_preferente) y tienen un Caso asignado a este usuario como abogado o procurador.
 * La visibilidad del ítem de menú la controla el permiso que Shield genera para esta página
 * (ver HasPageShield); los datos ya vienen filtrados por profesional independientemente de eso.
 */
class ClientesEjecutivos extends Page implements HasTable
{
    use HasPageShield;
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Clientes Ejecutivos';

    protected static ?string $title = 'Clientes Ejecutivos';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Cliente::query()
                    ->where('es_preferente', true)
                    ->whereHas('casos', fn (Builder $query) => $query
                        ->where('abogado_id', Auth::id())
                        ->orWhere('procurador_id', Auth::id()))
            )
            ->defaultSort('nombres')
            ->recordAction('verExpediente')
            ->columns([
                TextColumn::make('nombre_completo')
                    ->label('Cliente')
                    ->searchable(['nombres', 'apellido_paterno', 'apellido_materno']),
                TextColumn::make('ci')
                    ->label('C.I.'),
                TextColumn::make('telefono')
                    ->label('Teléfono'),
                TextColumn::make('correo')
                    ->label('Correo')
                    ->searchable(),
                TextColumn::make('casos_count')
                    ->label('Casos')
                    ->state(fn (Cliente $record) => $record->casos()
                        ->where(fn (Builder $query) => $query
                            ->where('abogado_id', Auth::id())
                            ->orWhere('procurador_id', Auth::id()))
                        ->count())
                    ->badge(),
            ])
            ->recordActions([
                VerExpedienteClienteAction::make(),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }
}
