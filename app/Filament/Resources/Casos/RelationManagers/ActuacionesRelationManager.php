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
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActuacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'actuaciones';

    protected static ?string $title = 'Actuaciones';

    // CU-014 Registrar Actuación Jurídica — el caso de uso más utilizado por los abogados.
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo')
                    ->options([
                        'Presentación de memorial' => 'Presentación de memorial',
                        'Audiencia' => 'Audiencia',
                        'Reunión' => 'Reunión',
                        'Llamada' => 'Llamada',
                        'Conciliación' => 'Conciliación',
                        'Revisión documental' => 'Revisión documental',
                        'Notificación' => 'Notificación',
                        'Recurso' => 'Recurso',
                        'Citación' => 'Citación',
                        'Apelación' => 'Apelación',
                    ])
                    ->required(),
                Select::make('responsable_id')
                    ->label('Responsable')
                    ->relationship('responsable', 'name')
                    ->searchable(),
                DatePicker::make('fecha')
                    ->default(now())
                    ->required(),
                TimePicker::make('hora'),
                Textarea::make('descripcion')
                    ->required()
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
                TextColumn::make('descripcion')
                    ->limit(60),
                TextColumn::make('responsable.name')
                    ->label('Responsable'),
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('hora')
                    ->time(),
            ])
            ->filters([
                //
            ])
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
