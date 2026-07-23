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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConciliacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'conciliaciones';

    protected static ?string $title = 'Conciliaciones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('lugar'),
                DatePicker::make('fecha')
                    ->required(),
                TextInput::make('participantes'),
                Textarea::make('resultado'),
                Textarea::make('acuerdos'),
                Textarea::make('documentacion')
                    ->label('Documentación')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('lugar')
            ->defaultSort('fecha', 'desc')
            ->columns([
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('lugar'),
                TextColumn::make('participantes'),
                TextColumn::make('resultado')
                    ->limit(50),
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
