<?php

namespace App\Filament\Resources\Contactos\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactosTable
{
    // RN CU-003: cada intento queda registrado y no puede eliminarse ni editarse — tabla de solo lectura + alta.
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('ticket.consulta.nombre')
                    ->label('Consulta')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Contactado por'),
                TextColumn::make('medio')
                    ->badge(),
                TextColumn::make('resultado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Contestó' => 'success',
                        'Reagenda', 'Solicita devolución' => 'warning',
                        'No contestó', 'Número incorrecto' => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('resultado')
                    ->options([
                        'Contestó' => 'Contestó',
                        'No contestó' => 'No contestó',
                        'Número incorrecto' => 'Número incorrecto',
                        'Solicita devolución' => 'Solicita devolución',
                        'Reagenda' => 'Reagenda',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
