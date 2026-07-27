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
            ->defaultSort('created_at', 'desc') // Orden descendente por fecha
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha de consulta')
                    ->dateTime()
                    ->sortable()
                    ->searchable(), // Añadido searchable para poder buscar por fecha
                
                TextColumn::make('cliente.nombres')
                    ->label('Nombre')
                    ->searchable(),
                
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                
                TextColumn::make('ciudad')
                    ->label('Ciudad')
                    ->toggleable(),
                
                TextColumn::make('tipo_proceso')
                    ->label('Tipo de proceso')
                    ->toggleable(),
                
                TextColumn::make('ticket.estado')
                    ->label('Estado')
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
                
                // Columnas ocultas por defecto pero disponibles para toggle
                TextColumn::make('email')
                    ->label('Correo')
                    ->toggleable(isToggledHiddenByDefault: true), // Oculto por defecto
                
                TextColumn::make('forma_ingreso')
                    ->label('Forma de ingreso')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true), // Oculto por defecto
                
                TextColumn::make('origen')
                    ->label('Origen')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true), // Oculto por defecto
                
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