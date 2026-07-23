<?php

namespace App\Filament\Resources\Consultas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ConsultasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at')
            ->columns([
                TextColumn::make('cliente.nombres')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('ciudad')
                    ->toggleable(),
                TextColumn::make('tipo_proceso')
                    ->label('Tipo de proceso')
                    ->toggleable(),
                TextColumn::make('forma_ingreso')
                    ->label('Forma de ingreso')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('origen')
                    ->badge(),
                TextColumn::make('ticket.estado')
                    ->label('Estado del ticket')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'Pendiente' => 'warning',
                        'Asignado', 'En Atención' => 'info',
                        'Cerrado' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('ticket.asignadoA.name')
                    ->label('Asignado a')
                    ->placeholder('Sin asignar'),
                TextColumn::make('created_at')
                    ->label('Ingresó')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
