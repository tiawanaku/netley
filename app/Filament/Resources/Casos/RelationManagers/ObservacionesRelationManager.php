<?php

namespace App\Filament\Resources\Casos\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ObservacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'observaciones';

    protected static ?string $title = 'Observaciones';

    // CU-015: agregar observaciones sin modificar el estado del caso. Queda "firmada digitalmente"
    // por el usuario que la registra -> no se puede editar ni borrar, solo crear y listar.
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('texto')
                    ->label('Observación')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('texto')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('texto')
                    ->label('Observación')
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label('Registrada por'),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
