<?php

namespace App\Filament\Resources\Tickets\Tables;

use App\Models\Ticket;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at')
            ->columns([
                TextColumn::make('consulta.nombre')
                    ->label('Consulta')
                    ->searchable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Pendiente' => 'warning',
                        'Asignado', 'En Atención' => 'info',
                        'Cerrado' => 'success',
                    }),
                TextColumn::make('prioridad')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Baja' => 'gray',
                        'Normal' => 'info',
                        'Alta' => 'warning',
                        'Urgente' => 'danger',
                    }),
                TextColumn::make('asignadoA.name')
                    ->label('Asignado a')
                    ->placeholder('Sin asignar'),
                TextColumn::make('fecha_asignacion')
                    ->label('Fecha de asignación')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Asignado' => 'Asignado',
                        'En Atención' => 'En atención',
                        'Cerrado' => 'Cerrado',
                    ]),
            ])
            ->recordActions([
                // CU-002 Asignar Consulta: asigna el ticket más antiguo (FIFO por defaultSort) al profesional elegido.
                Action::make('asignar')
                    ->label('Asignar')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn (Ticket $record) => $record->estado === 'Pendiente')
                    ->schema([
                        Select::make('user_id')
                            ->label('Profesional')
                            ->options(User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (Ticket $record, array $data) {
                        $record->asignarA(User::findOrFail($data['user_id']));

                        Notification::make()
                            ->title('Ticket asignado')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
