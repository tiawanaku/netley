<?php

namespace App\Filament\Resources\Casos\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResolucionesJudicialesRelationManager extends RelationManager
{
    protected static string $relationship = 'resolucionesJudiciales';

    protected static ?string $title = 'Resoluciones judiciales';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tipo')
                    ->required(),
                TextInput::make('numero')
                    ->label('N.º'),
                DatePicker::make('fecha')
                    ->required(),
                TextInput::make('tribunal'),
                Textarea::make('resultado')
                    ->columnSpanFull(),
                FileUpload::make('adjunto')
                    ->disk('public')
                    ->directory('resoluciones'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo')
            ->defaultSort('fecha', 'desc')
            ->columns([
                TextColumn::make('tipo'),
                TextColumn::make('numero')
                    ->label('N.º'),
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('tribunal'),
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
