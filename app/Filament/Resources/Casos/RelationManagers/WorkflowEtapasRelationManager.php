<?php

namespace App\Filament\Resources\Casos\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkflowEtapasRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowEtapas';

    protected static ?string $title = 'Workflow procesal';

    // CU-012: las etapas generadas desde la plantilla de la materia son "originales" (RN-032: no se
    // pueden eliminar). El abogado puede agregar etapas propias (RN-031), esas sí son eliminables.
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('orden')
                    ->numeric()
                    ->required(),
                DatePicker::make('fecha_estimada'),
                DatePicker::make('fecha_real'),
                Toggle::make('completada'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->defaultSort('orden')
            ->columns([
                TextColumn::make('orden')
                    ->sortable(),
                TextColumn::make('nombre'),
                TextColumn::make('fecha_estimada')
                    ->label('Fecha estimada')
                    ->date(),
                TextColumn::make('fecha_real')
                    ->label('Fecha real')
                    ->date(),
                IconColumn::make('completada')
                    ->boolean(),
                IconColumn::make('es_original')
                    ->label('Original')
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Action::make('generarWorkflow')
                    ->label('Generar workflow desde plantilla')
                    ->icon('heroicon-o-sparkles')
                    ->visible(fn () => ! $this->getOwnerRecord()->workflowEtapas()->where('es_original', true)->exists())
                    ->action(function () {
                        $this->getOwnerRecord()->generarWorkflow();

                        Notification::make()->title('Workflow generado')->success()->send();
                    }),
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['es_original'] = false;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record) => ! $record->es_original),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
