<?php

namespace App\Filament\Resources\Casos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CasosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('codigo')
                    ->searchable(),
                TextColumn::make('cliente.nombres')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('abogado.name')
                    ->label('Abogado')
                    ->searchable(),
                TextColumn::make('procurador.name')
                    ->label('Procurador')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('especialidad')
                    ->searchable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Abierto' => 'info',
                        'En Proceso' => 'warning',
                        'Suspendido' => 'gray',
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
                TextColumn::make('fecha_inicio')
                    ->date()
                    ->sortable(),
                TextColumn::make('fecha_fin')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('numero_expediente')
                    ->label('N.º expediente')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('juzgado')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'Abierto' => 'Abierto',
                        'En Proceso' => 'En proceso',
                        'Suspendido' => 'Suspendido',
                        'Cerrado' => 'Cerrado',
                    ]),
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
