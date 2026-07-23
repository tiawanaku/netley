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
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DiligenciasRelationManager extends RelationManager
{
    protected static string $relationship = 'diligencias';

    protected static ?string $title = 'Diligencias';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo')
                    ->options([
                        'Notificación' => 'Notificación',
                        'Citación' => 'Citación',
                        'Embargo' => 'Embargo',
                        'Inspección' => 'Inspección',
                        'Allanamiento' => 'Allanamiento',
                        'Entrega' => 'Entrega',
                        'Otro' => 'Otro',
                    ])
                    ->required(),
                DatePicker::make('fecha')
                    ->required(),
                TextInput::make('resultado'),
                Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo')
            ->defaultSort('fecha', 'desc')
            ->columns([
                TextColumn::make('tipo')
                    ->badge(),
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
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
