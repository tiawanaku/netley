<?php

namespace App\Filament\Resources\Casos\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SolicitudesDocumentoRelationManager extends RelationManager
{
    protected static string $relationship = 'solicitudesDocumento';

    protected static ?string $title = 'Solicitudes de documentación';

    // CU-019 Solicitar Documentación al Cliente. La notificación real por Portal Cliente queda
    // pendiente hasta que exista el Dominio 5 — aquí solo se registra la solicitud y su estado.
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('descripcion')
                    ->required()
                    ->columnSpanFull(),
                DatePicker::make('fecha_limite')
                    ->label('Fecha límite')
                    ->required(),
                Select::make('estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Recibido' => 'Recibido',
                        'Vencido' => 'Vencido',
                    ])
                    ->default('Pendiente')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->defaultSort('fecha_limite')
            ->columns([
                TextColumn::make('descripcion')
                    ->limit(60),
                TextColumn::make('fecha_limite')
                    ->label('Fecha límite')
                    ->date()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Pendiente' => 'warning',
                        'Recibido' => 'success',
                        'Vencido' => 'danger',
                    }),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
