<?php

namespace App\Filament\Resources\Casos\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AudienciasRelationManager extends RelationManager
{
    protected static string $relationship = 'audiencias';

    protected static ?string $title = 'Audiencias';

    // CU-020: los recordatorios automáticos a abogado/procurador/cliente quedan pendientes hasta
    // que exista el Dominio 9 (Notificaciones). Aquí solo se registra la audiencia.
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tipo')
                    ->required(),
                TextInput::make('tribunal'),
                TextInput::make('juez'),
                DatePicker::make('fecha')
                    ->required(),
                TimePicker::make('hora'),
                TextInput::make('sala'),
                TextInput::make('participantes'),
                Textarea::make('observaciones')
                    ->columnSpanFull(),
                TextInput::make('resultado'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo')
            ->defaultSort('fecha')
            ->columns([
                TextColumn::make('tipo'),
                TextColumn::make('tribunal'),
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('hora')
                    ->time(),
                TextColumn::make('sala'),
                TextColumn::make('resultado'),
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
