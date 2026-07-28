<?php

namespace App\Filament\Resources\Citas\Actions;

use App\Filament\Resources\Citas\Schemas\ResultadoCitaForm;
use App\Models\Caso;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\CuotaPago;
use App\Models\Documento;
use App\Models\Pago;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Acción "Ver / registrar resultado" en la bandeja Reuniones Agendadas. A diferencia de un
 * EditAction normal, este formulario combina varios modelos (Cita, Cliente, Caso, Pago,
 * CuotaPago, Documento) en una sola pantalla, así que fillForm()/action() se arman a mano en
 * vez de delegarlos a la relación estándar de un Resource.
 */
class RegistrarResultadoCitaAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'registrarResultado';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Ver / registrar resultado');
        $this->modalHeading('Resultado de la cita');
        $this->modalSubmitActionLabel('Guardar');
        $this->modalWidth('5xl');
        $this->schema(ResultadoCitaForm::components());

        $this->fillForm(function (Cita $record): array {
            $cliente = $record->cliente;
            $caso = Caso::query()->where('cliente_id', $cliente->id)->latest()->first();

            return [
                'cliente_ci' => $cliente->ci,
                'determinacion' => $record->determinacion ?? [],
                'pendiente' => $record->pendiente,
                'pendiente_llamar_fecha' => $record->pendiente_llamar_fecha,
                'pendiente_llamar_hora' => $record->pendiente_llamar_hora,
                'hora_inicio_reunion' => $record->hora_inicio_reunion,
                'hora_fin_reunion' => $record->hora_fin_reunion,
                'cliente_nombres' => $cliente->nombres,
                'cliente_apellido_paterno' => $cliente->apellido_paterno,
                'cliente_apellido_materno' => $cliente->apellido_materno,
                'cliente_telefono' => $cliente->telefono,
                'cliente_whatsapp' => $cliente->whatsapp,
                'area' => $caso?->especialidad,
                'tipo_proceso' => $caso?->tipo,
                'abogado1_id' => $caso?->abogado_id,
                'abogado2_id' => $caso?->procurador_id,
                'tiempo_proceso_meses' => $caso?->tiempo_proceso_meses,
                'requiere_poder' => $caso?->requiere_poder ?? false,
                'monto_iguala_profesional' => $caso?->monto_iguala_profesional,
                'monto_comision' => $caso?->monto_comision,
                'comision_porcentaje' => $caso?->comision_porcentaje,
                'cuotas' => $caso?->cuotasPago->map(fn (CuotaPago $cuota) => ['monto' => (string) $cuota->monto])->all() ?? [],
            ];
        });

        $this->action(function (Cita $record, array $data): void {
            $caso = DB::transaction(fn () => $this->guardar($record, $data));

            $this->success();

            if ($caso) {
                Notification::make()
                    ->title('Caso creado')
                    ->body('Ya puedes descargar el contrato de prestación de servicios para que lo firme el cliente.')
                    ->success()
                    ->actions([
                        Action::make('descargarContrato')
                            ->label('Descargar contrato')
                            ->url(route('casos.contrato', $caso), shouldOpenInNewTab: true),
                    ])
                    ->persistent()
                    ->send();
            }
        });
    }

    protected function guardar(Cita $record, array $data): ?Caso
    {
        $cliente = $record->cliente;

        $cliente->update([
            'ci' => $data['cliente_ci'] ?: $cliente->ci,
            'nombres' => $data['cliente_nombres'],
            'apellido_paterno' => $data['cliente_apellido_paterno'] ?? null,
            'apellido_materno' => $data['cliente_apellido_materno'] ?? null,
            'telefono' => $data['cliente_telefono'] ?? null,
            'whatsapp' => $data['cliente_whatsapp'] ?? null,
        ]);

        $record->update([
            'determinacion' => $data['determinacion'] ?? [],
            'pendiente' => (bool) ($data['pendiente'] ?? false),
            'pendiente_llamar_fecha' => $data['pendiente'] ? ($data['pendiente_llamar_fecha'] ?? null) : null,
            'pendiente_llamar_hora' => $data['pendiente'] ? ($data['pendiente_llamar_hora'] ?? null) : null,
            'hora_inicio_reunion' => $data['hora_inicio_reunion'] ?? null,
            'hora_fin_reunion' => $data['hora_fin_reunion'] ?? null,
        ]);

        $caso = null;

        if (filled($data['area'] ?? null)) {
            $caso = Caso::create([
                'cliente_id' => $cliente->id,
                'abogado_id' => $data['abogado1_id'] ?? null,
                'procurador_id' => $data['abogado2_id'] ?? null,
                'especialidad' => $data['area'],
                'tipo' => $data['tipo_proceso'] ?? null,
                'fecha_inicio' => now(),
                'tiempo_proceso_meses' => $data['tiempo_proceso_meses'] ?: null,
                'requiere_poder' => (bool) ($data['requiere_poder'] ?? false),
                'monto_iguala_profesional' => $data['monto_iguala_profesional'] ?: null,
                'monto_comision' => $data['monto_comision'] ?: null,
                'comision_porcentaje' => $data['comision_porcentaje'] ?: null,
            ]);
            $caso->generarWorkflow();

            foreach (array_values($data['cuotas'] ?? []) as $indice => $cuota) {
                if (blank($cuota['monto'] ?? null)) {
                    continue;
                }

                CuotaPago::create([
                    'caso_id' => $caso->id,
                    'numero' => $indice + 1,
                    'monto' => $cuota['monto'],
                ]);
            }

            foreach (($data['documentos'] ?? []) as $documento) {
                if (blank($documento['archivo'] ?? null)) {
                    continue;
                }

                Documento::create([
                    'caso_id' => $caso->id,
                    'propietario_id' => Auth::id(),
                    'nombre' => basename((string) $documento['archivo']),
                    'tipo' => Str::endsWith($documento['archivo'], ['.jpg', '.jpeg', '.png', '.gif', '.webp']) ? 'Imagen' : 'PDF',
                    'formato' => $documento['formato'] ?? null,
                    'archivo' => $documento['archivo'],
                    'permisos' => 'Solo abogado',
                ]);
            }
        }

        if (filled($data['anticipo'] ?? null) && filled($data['numero_recibo'] ?? null)) {
            Pago::create([
                'cliente_id' => $cliente->id,
                'consulta_id' => $record->consulta_id,
                'numero_recibo' => $data['numero_recibo'],
                'monto' => $data['anticipo'],
                'fecha_pago' => now(),
            ]);
        }

        if (filled($data['proxima_reunion_fecha'] ?? null) && filled($data['proxima_reunion_hora'] ?? null)) {
            $consultaSiguiente = null;

            if (filled($data['proxima_reunion_tema'] ?? null)) {
                $consultaSiguiente = Consulta::create([
                    'cliente_id' => $cliente->id,
                    'nombre' => $cliente->nombres,
                    'apellido_paterno' => $cliente->apellido_paterno,
                    'apellido_materno' => $cliente->apellido_materno,
                    'email' => $cliente->correo,
                    'telefono' => $cliente->telefono,
                    'descripcion' => $data['proxima_reunion_tema'],
                    'origen' => 'landing',
                ]);
            }

            Cita::create([
                'cliente_id' => $cliente->id,
                'consulta_id' => $consultaSiguiente?->id,
                'asignado_a_user_id' => $record->asignado_a_user_id,
                'fecha_hora' => Carbon::parse($data['proxima_reunion_fecha'].' '.$data['proxima_reunion_hora']),
                'modalidad' => $record->modalidad,
                'estado' => 'Pendiente',
            ]);
        }

        return $caso;
    }
}
