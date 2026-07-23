<?php

namespace App\Filament\Resources\Contactos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ticket_id')
                    ->label('Ticket')
                    ->relationship('ticket', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->consulta->nombre.' — #'.$record->id)
                    ->searchable()
                    ->required(),
                Select::make('medio')
                    ->options([
                        'Teléfono' => 'Teléfono',
                        'WhatsApp' => 'WhatsApp',
                        'Email' => 'Email',
                        'Presencial' => 'Presencial',
                    ])
                    ->required(),
                Select::make('resultado')
                    ->options([
            'Contestó' => 'Contestó',
            'No contestó' => 'No contestó',
            'Número incorrecto' => 'Número incorrecto',
            'Solicita devolución' => 'Solicita devolución',
            'Reagenda' => 'Reagenda',
        ])
                    ->required(),
                Textarea::make('notas')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
