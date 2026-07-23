<?php

namespace App\Filament\Resources\Casos\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentosRelationManager extends RelationManager
{
    protected static string $relationship = 'documentos';

    protected static ?string $title = 'Documentos';

    // CU-018 Registrar Documento: todo documento posee version, propietario, fecha, tamaño, hash y permisos.
    // El propietario es siempre quien sube el documento, no se pide en el formulario.
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Select::make('tipo')
                    ->options([
                        'Memorial' => 'Memorial',
                        'Poder' => 'Poder',
                        'Demanda' => 'Demanda',
                        'Contestación' => 'Contestación',
                        'Sentencia' => 'Sentencia',
                        'Audio' => 'Audio',
                        'Video' => 'Video',
                        'Imagen' => 'Imagen',
                        'PDF' => 'PDF',
                        'Oficio' => 'Oficio',
                        'Resolución' => 'Resolución',
                    ])
                    ->required(),
                FileUpload::make('archivo')
                    ->disk('public')
                    ->directory('documentos-casos')
                    ->required(),
                Select::make('permisos')
                    ->options([
                        'Solo abogado' => 'Solo abogado',
                        'Abogado y cliente' => 'Abogado y cliente',
                        'Público' => 'Público',
                    ])
                    ->default('Solo abogado')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('tipo')
                    ->badge(),
                TextColumn::make('version'),
                TextColumn::make('propietario.name')
                    ->label('Propietario'),
                TextColumn::make('tamano')
                    ->label('Tamaño')
                    ->formatStateUsing(fn (?int $state) => $state ? number_format($state / 1024, 1).' KB' : '—'),
                TextColumn::make('permisos')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['propietario_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
