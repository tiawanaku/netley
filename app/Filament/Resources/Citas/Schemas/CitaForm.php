<?php

namespace App\Filament\Resources\Citas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class CitaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::components());
    }

    /**
     * @param  bool  $incluirCliente  false cuando este formulario se embebe en el wizard de Crear
     *                                 Consulta: ahí el cliente ya se elige en el paso "Consulta" y la
     *                                 cita se registra automáticamente para ese mismo cliente, sin
     *                                 volver a preguntarlo (el valor de 'cliente_id' ya viaja en el
     *                                 estado compartido del wizard).
     * @param  bool  $incluirConsultaRelacionada  false cuando este formulario se embebe en el wizard de
     *                                             Crear Consulta: ahí la cita siempre queda ligada a la
     *                                             consulta recién creada, así que el selector de una
     *                                             consulta existente no aplica.
     * @param  bool  $incluirAsignacion  false cuando este formulario se embebe en el wizard de Crear
     *                                    Consulta: la cita recién creada todavía no tiene a quién
     *                                    asignarse, eso lo hace el administrador después, editando la
     *                                    cita ya creada.
     */
    public static function components(bool $incluirCliente = true, bool $incluirConsultaRelacionada = true, bool $incluirAsignacion = true): array
    {
        return [
            ...($incluirCliente ? [
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombres')
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('consulta_id', null))
                    ->required(),
            ] : []),
            ...($incluirConsultaRelacionada ? [
                Select::make('consulta_id')
                    ->label('Consulta relacionada')
                    ->relationship(
                        name: 'consulta',
                        titleAttribute: 'descripcion',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->when($get('cliente_id'), fn (Builder $query, $clienteId) => $query->where('cliente_id', $clienteId))
                            ->orderBy('created_at'),
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->descripcion ?: "Consulta #{$record->id}")
                    ->searchable()
                    ->default(null),
            ] : []),
            DatePicker::make('cita_fecha')
                ->label('Fecha')
                ->required()
                ->live()
                ->dehydrated(false)
                ->afterStateHydrated(function (Set $set, Get $get) {
                    if (filled($fechaHora = $get('fecha_hora'))) {
                        $set('cita_fecha', Carbon::parse($fechaHora)->format('Y-m-d'));
                    }
                })
                ->afterStateUpdated(fn (Set $set, Get $get) => $set('fecha_hora', self::combinarFechaHora($get('cita_fecha'), $get('cita_hora')))),
            Select::make('cita_hora')
                ->label('Hora')
                ->options(function (Get $get) {
                    $opciones = self::opcionesHorario();

                    // Citas ya existentes pueden tener una hora fuera del horario de atención o
                    // que no cae en un múltiplo de 15 minutos (registradas antes de este cambio).
                    // Se agrega su hora exacta como opción para no perder ni alterar ese dato al
                    // editar la cita por otro motivo (p.ej. solo para asignarla a alguien).
                    if (filled($fechaHora = $get('fecha_hora'))) {
                        $horaExistente = Carbon::parse($fechaHora)->format('H:i');
                        $opciones[$horaExistente] ??= Carbon::parse($fechaHora)->format('h:i a');
                    }

                    return $opciones;
                })
                ->required()
                ->live()
                ->disabled(fn (Get $get) => blank($get('cita_fecha')))
                ->dehydrated(false)
                ->afterStateHydrated(function (Set $set, Get $get) {
                    if (filled($fechaHora = $get('fecha_hora'))) {
                        $set('cita_hora', Carbon::parse($fechaHora)->format('H:i'));
                    }
                })
                ->afterStateUpdated(fn (Set $set, Get $get) => $set('fecha_hora', self::combinarFechaHora($get('cita_fecha'), $get('cita_hora')))),
            Hidden::make('fecha_hora')
                ->required(),
            Select::make('modalidad')
                ->options(['Presencial' => 'Presencial', 'Virtual' => 'Virtual'])
                ->required(),
            Select::make('estado')
                ->options([
                    'Pendiente' => 'Pendiente',
                    'Confirmada' => 'Confirmada',
                    'Cancelada' => 'Cancelada',
                    'Reagendada' => 'Reagendada',
                    'Realizada' => 'Realizada',
                ])
                ->default('Pendiente')
                ->required(),
            ...($incluirAsignacion ? [
                Select::make('asignado_a_user_id')
                    ->label('Asignar a')
                    ->helperText('El miembro del personal (abogado, psicólogo, pasante, etc.) que verá esta cita en su bandeja "Reuniones Agendadas".')
                    ->relationship('asignadoA', 'name')
                    ->searchable()
                    ->default(null),
            ] : []),
            Textarea::make('notas')
                ->default(null)
                ->columnSpanFull(),
            // CU-005 Registrar Resultado de la Cita (lo llena el abogado tras la reunión)
            Section::make('Resultado de la cita')
                ->columns(2)
                ->collapsed()
                ->schema([
                    Textarea::make('diagnostico_preliminar')
                        ->label('Diagnóstico preliminar')
                        ->columnSpanFull(),
                    TextInput::make('tipo_servicio')
                        ->label('Tipo de servicio requerido'),
                    TextInput::make('costo')
                        ->label('Costo (Bs.)')
                        ->numeric(),
                    Select::make('riesgo')
                        ->options(['Bajo' => 'Bajo', 'Medio' => 'Medio', 'Alto' => 'Alto']),
                    Select::make('deriva_a')
                        ->label('Deriva a')
                        ->options([
                            'Legal' => 'Legal',
                            'Psicología' => 'Psicología',
                            'Conciliación' => 'Conciliación',
                            'Otro' => 'Otro',
                        ]),
                    Textarea::make('recomendaciones')
                        ->columnSpanFull(),
                    Textarea::make('observaciones')
                        ->columnSpanFull(),
                ]),
        ];
    }

    protected static function combinarFechaHora(?string $fecha, ?string $hora): ?string
    {
        if (blank($fecha) || blank($hora)) {
            return null;
        }

        return Carbon::parse("{$fecha} {$hora}")->format('Y-m-d H:i:s');
    }

    /**
     * Horarios de atención: 8am a 6pm en intervalos de 15 minutos.
     *
     * @return array<string, string>
     */
    protected static function opcionesHorario(): array
    {
        $opciones = [];

        for ($minutos = 8 * 60; $minutos <= 18 * 60; $minutos += 15) {
            $hora = Carbon::createFromTime(0, 0)->addMinutes($minutos);
            $opciones[$hora->format('H:i')] = $hora->format('h:i a');
        }

        return $opciones;
    }
}
