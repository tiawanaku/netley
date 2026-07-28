<?php

namespace App\Filament\Resources\Citas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CitasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_hora')
            ->columns([
                TextColumn::make('cliente.nombres')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('asignadoA.name')
                    ->label('Asignado a')
                    ->placeholder('Sin asignar')
                    ->searchable(),
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
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
