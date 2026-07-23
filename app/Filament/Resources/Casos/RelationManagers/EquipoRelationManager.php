<?php

namespace App\Filament\Resources\Casos\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EquipoRelationManager extends RelationManager
{
    protected static string $relationship = 'equipo';

    protected static ?string $title = 'Equipo jurídico';

    // CU-011 Asignar Equipo Jurídico. El abogado patrocinador y el procurador principal se
    // asignan directamente en el formulario del caso (CU-008); aquí van los demás colaboradores
    // (RN-021: un caso puede tener varios colaboradores).
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('rol')
                    ->options([
                        'Pasante' => 'Pasante',
                        'Psicólogo' => 'Psicólogo',
                        'Conciliador' => 'Conciliador',
                        'Colaborador' => 'Colaborador',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('pivot.rol')
                    ->label('Rol')
                    ->badge(),
            ])
            ->filters([])
            ->headerActions([
                AttachAction::make()
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('rol')
                            ->options([
                                'Pasante' => 'Pasante',
                                'Psicólogo' => 'Psicólogo',
                                'Conciliador' => 'Conciliador',
                                'Colaborador' => 'Colaborador',
                            ])
                            ->required(),
                    ]),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
