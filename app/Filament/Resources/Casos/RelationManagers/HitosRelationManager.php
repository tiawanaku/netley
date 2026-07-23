<?php

namespace App\Filament\Resources\Casos\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HitosRelationManager extends RelationManager
{
    protected static string $relationship = 'hitos';

    protected static ?string $title = 'Hitos procesales';

    // CU-016 Registrar Hito Procesal + CU-017 Registrar Retraso Procesal (justificación plegada).
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Select::make('responsable_id')
                    ->label('Responsable')
                    ->relationship('responsable', 'name')
                    ->searchable(),
                DatePicker::make('fecha_prevista')
                    ->required(),
                DatePicker::make('fecha_real'),
                TextInput::make('resultado'),
                Textarea::make('comentario')
                    ->columnSpanFull(),
                Section::make('Retraso procesal (si aplica)')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Select::make('causa_retraso')
                            ->label('Causa del retraso')
                            ->options([
                                'Retraso judicial' => 'Retraso judicial',
                                'Retraso del cliente' => 'Retraso del cliente',
                                'Falta de documentos' => 'Falta de documentos',
                                'Huelga' => 'Huelga',
                                'Vacación judicial' => 'Vacación judicial',
                                'Falta de pago' => 'Falta de pago',
                                'Investigación' => 'Investigación',
                                'Fuerza mayor' => 'Fuerza mayor',
                                'Otro' => 'Otro',
                            ]),
                        FileUpload::make('evidencia_retraso')
                            ->label('Evidencia'),
                        Textarea::make('explicacion_retraso')
                            ->label('Explicación')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->defaultSort('fecha_prevista')
            ->columns([
                TextColumn::make('nombre'),
                TextColumn::make('fecha_prevista')
                    ->label('Fecha prevista')
                    ->date()
                    ->sortable(),
                TextColumn::make('fecha_real')
                    ->label('Fecha real')
                    ->date(),
                TextColumn::make('responsable.name')
                    ->label('Responsable'),
                IconColumn::make('esta_retrasado_justificado')
                    ->label('Retraso justificado')
                    ->boolean(),
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
