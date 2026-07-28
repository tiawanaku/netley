<?php

namespace App\Filament\Resources\Clientes\Actions;

use App\Models\Caso;
use App\Models\Cliente;
use App\Models\Documento;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * "Ver expediente" en Clientes Ejecutivos: muestra los datos del cliente, sus Casos con el
 * profesional que está viendo la pantalla, los documentos ya subidos, y permite subir nuevos
 * (por ejemplo, el escaneado del contrato firmado).
 */
class VerExpedienteClienteAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'verExpediente';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Ver expediente');
        $this->modalHeading('Expediente del cliente');
        $this->modalSubmitActionLabel('Subir documentos');
        $this->modalWidth('4xl');

        $this->schema([
            Section::make('Cliente')
                ->columns(3)
                ->schema([
                    Placeholder::make('info_nombre')
                        ->label('Nombre')
                        ->content(fn (Cliente $record) => $record->nombre_completo),
                    Placeholder::make('info_ci')
                        ->label('C.I.')
                        ->content(fn (Cliente $record) => $record->ci ?? '—'),
                    Placeholder::make('info_telefono')
                        ->label('Teléfono')
                        ->content(fn (Cliente $record) => $record->telefono ?? '—'),
                    Placeholder::make('info_correo')
                        ->label('Correo')
                        ->content(fn (Cliente $record) => $record->correo ?? '—'),
                ]),

            Section::make('Casos asignados a este profesional')
                ->schema([
                    Placeholder::make('info_casos')
                        ->label('')
                        ->content(fn (Cliente $record) => self::listaCasosHtml(self::casosDelProfesional($record))),
                ]),

            Section::make('Documentos ya subidos')
                ->schema([
                    Placeholder::make('info_documentos')
                        ->label('')
                        ->content(fn (Cliente $record) => self::listaDocumentosHtml(self::casosDelProfesional($record))),
                ]),

            Section::make('Subir al expediente')
                ->schema([
                    Repeater::make('documentos')
                        ->label('Documentos')
                        ->schema([
                            Select::make('caso_id')
                                ->label('Caso')
                                ->options(fn (Cliente $record) => self::casosDelProfesional($record)->pluck('codigo', 'id'))
                                ->required(),
                            FileUpload::make('archivo')
                                ->label('Archivo')
                                ->disk('public')
                                ->directory('documentos-casos')
                                ->required(),
                            Select::make('tipo')
                                ->label('Tipo')
                                ->options([
                                    'Contrato' => 'Contrato',
                                    'Poder' => 'Poder',
                                    'Memorial' => 'Memorial',
                                    'Demanda' => 'Demanda',
                                    'Contestación' => 'Contestación',
                                    'Sentencia' => 'Sentencia',
                                    'Oficio' => 'Oficio',
                                    'Resolución' => 'Resolución',
                                    'Imagen' => 'Imagen',
                                    'PDF' => 'PDF',
                                    'Audio' => 'Audio',
                                    'Video' => 'Video',
                                ])
                                ->default('Contrato')
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
                        ->helperText('El cliente debe tener al menos un Caso asignado a este profesional (se registra desde "Reuniones Agendadas") para poder elegirlo aquí.')
                        ->reorderable(false),
                ]),
        ]);

        $this->action(function (Cliente $record, array $data): void {
            foreach (($data['documentos'] ?? []) as $documento) {
                if (blank($documento['archivo'] ?? null) || blank($documento['caso_id'] ?? null)) {
                    continue;
                }

                Documento::create([
                    'caso_id' => $documento['caso_id'],
                    'propietario_id' => Auth::id(),
                    'nombre' => basename((string) $documento['archivo']),
                    'tipo' => $documento['tipo'],
                    'formato' => $documento['formato'] ?? null,
                    'archivo' => $documento['archivo'],
                    'permisos' => 'Solo abogado',
                ]);
            }

            $this->success();
        });
    }

    /**
     * @return \Illuminate\Support\Collection<int, Caso>
     */
    protected static function casosDelProfesional(Cliente $record): \Illuminate\Support\Collection
    {
        return $record->casos()
            ->where(fn ($query) => $query
                ->where('abogado_id', Auth::id())
                ->orWhere('procurador_id', Auth::id()))
            ->get();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Caso>  $casos
     */
    protected static function listaCasosHtml(\Illuminate\Support\Collection $casos): HtmlString
    {
        if ($casos->isEmpty()) {
            return new HtmlString('<p>Sin casos asignados a este profesional.</p>');
        }

        $items = $casos->map(fn (Caso $caso) => sprintf(
            '<li><strong>%s</strong> — %s%s (%s)</li>',
            e($caso->codigo ?? "Caso #{$caso->id}"),
            e($caso->especialidad),
            $caso->tipo ? ' - '.e($caso->tipo) : '',
            e($caso->estado),
        ))->implode('');

        return new HtmlString("<ul>{$items}</ul>");
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Caso>  $casos
     */
    protected static function listaDocumentosHtml(\Illuminate\Support\Collection $casos): HtmlString
    {
        $documentos = Documento::query()
            ->whereIn('caso_id', $casos->pluck('id'))
            ->latest()
            ->get();

        if ($documentos->isEmpty()) {
            return new HtmlString('<p>Todavía no hay documentos subidos.</p>');
        }

        $items = $documentos->map(fn (Documento $documento) => sprintf(
            '<li><a href="%s" target="_blank">%s</a> — %s%s</li>',
            e(Str::start($documento->archivo, '/storage/')),
            e($documento->nombre),
            e($documento->tipo),
            $documento->formato ? ' ('.e($documento->formato).')' : '',
        ))->implode('');

        return new HtmlString("<ul>{$items}</ul>");
    }
}
