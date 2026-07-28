<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Citas\Actions\RegistrarResultadoCitaAction;
use App\Models\Cita;
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
use Illuminate\Support\Facades\Auth;

/**
 * Bandeja personal del personal (abogado, psicólogo, pasante, etc.): muestra únicamente las
 * citas que el administrador le asignó (Cita::asignado_a_user_id), no todas las citas del
 * sistema. La visibilidad del ítem de menú se controla con el permiso que Shield genera para
 * esta página (ver HasPageShield) — el administrador decide, desde Roles, a qué roles se la
 * habilita.
 */
class ReunionesAgendadas extends Page implements HasTable
{
    use HasPageShield;
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Reuniones Agendadas';

    protected static ?string $title = 'Reuniones Agendadas';

    public function table(Table $table): Table
    {
        return $table
            ->query(Cita::query()->where('asignado_a_user_id', Auth::id()))
            ->defaultSort('fecha_hora')
            ->recordAction('registrarResultado')
            ->columns([
                TextColumn::make('cliente.nombres')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('consulta.descripcion')
                    ->label('Tema a tratar')
                    ->wrap()
                    ->placeholder('—'),
                TextColumn::make('fecha_hora')
                    ->label('Fecha y hora')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('modalidad')
                    ->badge(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Pendiente' => 'warning',
                        'Confirmada' => 'info',
                        'Realizada' => 'success',
                        'Cancelada' => 'danger',
                        'Reagendada' => 'gray',
                    }),
            ])
            ->recordActions([
                RegistrarResultadoCitaAction::make(),
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
