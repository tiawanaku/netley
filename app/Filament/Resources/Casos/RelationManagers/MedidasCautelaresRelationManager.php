<?php

namespace App\Filament\Resources\Casos\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MedidasCautelaresRelationManager extends RelationManager
{
    protected static string $relationship = 'medidasCautelares';

    protected static ?string $title = 'Medidas cautelares';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tipo')
                    ->required(),
                Select::make('responsable_id')
                    ->label('Responsable')
                    ->relationship('responsable', 'name')
                    ->searchable(),
                DatePicker::make('fecha')
                    ->required(),
                Select::make('estado')
                    ->options([
                        'Solicitada' => 'Solicitada',
                        'Vigente' => 'Vigente',
                        'Levantada' => 'Levantada',
                        'Vencida' => 'Vencida',
                    ])
                    ->default('Solicitada')
                    ->required(),
                DatePicker::make('vigencia_hasta')
                    ->label('Vigencia hasta'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo')
            ->defaultSort('fecha', 'desc')
            ->columns([
                TextColumn::make('tipo'),
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge(),
                TextColumn::make('vigencia_hasta')
                    ->label('Vigencia hasta')
                    ->date(),
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
