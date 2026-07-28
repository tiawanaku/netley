<?php

namespace App\Filament\Resources\Citas\Schemas;

use App\Models\Cita;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

/**
 * Formulario de "Resultado de la cita" que se abre en el modal de Reuniones Agendadas
 * (RegistrarResultadoCitaAction). No es un formulario de un solo modelo: combina datos de
 * Cita, Cliente, Caso, Pago, CuotaPago y Documento en una sola pantalla; el mapeo hacia cada
 * modelo se resuelve en RegistrarResultadoCitaAction::guardar(), no aquí.
 */
class ResultadoCitaForm
{
    public static function components(): array
    {
        return [
            Section::make('Datos de la reunión')
                ->columns(4)
                ->schema([
                    Placeholder::make('info_cliente')
                        ->label('Cliente')
                        ->content(fn (Cita $record) => $record->cliente?->nombre_completo ?? '—'),
                    Placeholder::make('info_consulta')
                        ->label('Consulta / tema a tratar')
                        ->content(fn (Cita $record) => $record->consulta?->descripcion ?? '—'),
                    Placeholder::make('info_fecha')
                        ->label('Fecha')
                        ->content(fn (Cita $record) => $record->fecha_hora?->translatedFormat('d/m/Y') ?? '—'),
                    Placeholder::make('info_hora')
                        ->label('Hora')
                        ->content(fn (Cita $record) => $record->fecha_hora?->format('h:i a') ?? '—'),
                ]),

            Section::make('Cliente')
                ->columns(3)
                ->schema([
                    TextInput::make('cliente_ci')
                        ->label('ID Cliente (C.I.)'),
                    CheckboxList::make('determinacion')
                        ->label('Determinación')
                        ->options([
                            'Ejecutivo' => 'Ejecutivo',
                            'Cerrar Caso' => 'Cerrar Caso',
                        ])
                        ->columnSpan(1),
                    Toggle::make('pendiente')
                        ->label('Pendiente')
                        ->live()
                        ->columnSpan(1),
                    DatePicker::make('pendiente_llamar_fecha')
                        ->label('Llamar en fecha')
                        ->visible(fn (Get $get) => (bool) $get('pendiente')),
                    Select::make('pendiente_llamar_hora')
                        ->label('Llamar a la hora')
                        ->options(self::opcionesHorario())
                        ->visible(fn (Get $get) => (bool) $get('pendiente')),

                    TextInput::make('area')
                        ->label('Área'),
                    TextInput::make('tipo_proceso')
                        ->label('Tipo de proceso'),
                    Select::make('abogado1_id')
                        ->label('Abogado 1')
                        ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable(),
                    Select::make('abogado2_id')
                        ->label('Abogado 2')
                        ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable(),

                    Placeholder::make('fecha_reunion_placeholder')
                        ->label('Fecha de la reunión')
                        ->content(fn (Cita $record) => $record->fecha_hora?->translatedFormat('d/m/Y') ?? '—'),
                    TextInput::make('hora_inicio_reunion')
                        ->label('Hora inicio de la reunión')
                        ->type('time'),
                    TextInput::make('hora_fin_reunion')
                        ->label('Hora fin de la reunión')
                        ->type('time'),

                    TextInput::make('cliente_nombres')
                        ->label('Nombre del cliente')
                        ->required(),
                    TextInput::make('cliente_apellido_paterno')
                        ->label('Apellido paterno'),
                    TextInput::make('cliente_apellido_materno')
                        ->label('Apellido materno'),
                    TextInput::make('cliente_telefono')
                        ->label('Teléfono')
                        ->tel(),
                    TextInput::make('cliente_whatsapp')
                        ->label('WhatsApp')
                        ->tel(),
                ]),

            Section::make('Próxima reunión')
                ->columns(3)
                ->schema([
                    DatePicker::make('proxima_reunion_fecha')
                        ->label('Fecha')
                        ->live(),
                    Select::make('proxima_reunion_hora')
                        ->label('Hora')
                        ->options(self::opcionesHorario())
                        ->disabled(fn (Get $get) => blank($get('proxima_reunion_fecha'))),
                    Textarea::make('proxima_reunion_tema')
                        ->label('Tema a tratar')
                        ->columnSpanFull(),
                ]),

            Section::make('Proceso')
                ->columns(3)
                ->schema([
                    TextInput::make('tiempo_proceso_meses')
                        ->label('Tiempo del proceso (meses)')
                        ->numeric()
                        ->minValue(1)
                        ->live(onBlur: true),
                    Toggle::make('requiere_poder')
                        ->label('Poder'),
                    TextInput::make('monto_iguala_profesional')
                        ->label('Monto iguala profesional (Bs.)')
                        ->numeric()
                        ->prefix('Bs.')
                        ->live(onBlur: true),
                    TextInput::make('monto_comision')
                        ->label('Monto comisión (Bs.)')
                        ->numeric()
                        ->prefix('Bs.'),
                    TextInput::make('comision_porcentaje')
                        ->label('Comisión (%)')
                        ->numeric()
                        ->suffix('%'),
                ]),

            Section::make('Pagos')
                ->columns(3)
                ->schema([
                    TextInput::make('anticipo')
                        ->label('Anticipo recibido (Bs.)')
                        ->numeric()
                        ->prefix('Bs.')
                        ->live(onBlur: true),
                    TextInput::make('numero_recibo')
                        ->label('N.º de recibo'),
                    Placeholder::make('saldo')
                        ->label('Saldo')
                        ->content(function (Get $get) {
                            $iguala = (float) ($get('monto_iguala_profesional') ?? 0);
                            $anticipo = (float) ($get('anticipo') ?? 0);

                            return 'Bs. '.number_format(max($iguala - $anticipo, 0), 2);
                        }),
                    Repeater::make('cuotas')
                        ->label('Plan de pagos')
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('monto')
                                ->label('Monto de la cuota (Bs.)')
                                ->numeric()
                                ->required()
                                ->prefix('Bs.'),
                        ])
                        ->defaultItems(0)
                        ->addActionLabel('Agregar cuota')
                        ->maxItems(fn (Get $get) => max((int) ($get('tiempo_proceso_meses') ?? 0), 1))
                        ->helperText('Cada cuota equivale a un mes; no pueden ser más cuotas que meses de proceso.')
                        ->reorderable(false),
                ]),

            Section::make('Documentos recibidos')
                ->schema([
                    Repeater::make('documentos')
                        ->label('Documentos')
                        ->schema([
                            FileUpload::make('archivo')
                                ->label('Archivo')
                                ->disk('public')
                                ->directory('documentos-casos')
                                ->required(),
                            Select::make('formato')
                                ->label('Formato')
                                ->options([
                                    'Original' => 'Original',
                                    'Legalizada' => 'Legalizada',
                                    'Copia simple' => 'Copia simple',
                                ])
                                ->required(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Agregar documento')
                        ->maxItems(7)
                        ->reorderable(false)
                        ->helperText('Hasta 7 documentos. Quedan asignados al cliente (dentro de su Caso).'),
                ]),
        ];
    }

    /**
     * Horarios de atención: 8am a 6pm en intervalos de 15 minutos (mismo criterio que CitaForm).
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
