<?php

namespace App\Filament\Resources\Consultas\Pages;

use App\Models\Cliente;
use App\Models\Consulta;
use App\Models\Cita;
use App\Filament\Resources\Consultas\ConsultaResource;
use App\Filament\Resources\Consultas\Schemas\ConsultaForm;
use App\Filament\Resources\Citas\Schemas\CitaForm;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Schemas\Components\Wizard\Step;

use Illuminate\Support\Facades\DB;

class CreateConsulta extends CreateRecord
{
    use HasWizard;

    protected static string $resource = ConsultaResource::class;

    /**
     * CU-001 Registrar Consulta: por comodidad de los usuarios, la Consulta y su primera Cita
     * se registran juntas en un solo wizard (lo correcto sería crear la Consulta y luego,
     * por separado, la Cita, pero así lo pide la cultura interna del despacho).
     */
    public function getSteps(): array
    {
        return [
            Step::make('Consulta')
                ->schema(ConsultaForm::components()),

            Step::make('Cita')
                ->schema(CitaForm::components(incluirCliente: false, incluirConsultaRelacionada: false)),
        ];
    }

    protected function handleRecordCreation(array $data): Consulta
    {
        return DB::transaction(function () use ($data) {

            // El cliente no se elige en el wizard: se resuelve aquí a partir de los datos de
            // contacto del paso "Consulta" (si ya existe alguien con ese correo, se reutiliza;
            // si no, se crea como Cliente nuevo).
            $cliente = Cliente::firstOrCreate(
                ['correo' => $data['email']],
                [
                    'nombres' => $data['nombre'],
                    'apellido_paterno' => $data['apellido_paterno'],
                    'apellido_materno' => $data['apellido_materno'],
                    'telefono' => $data['telefono'],
                    'ciudad' => $data['ciudad'],
                ]
            );

            $consulta = Consulta::create([

                'cliente_id'=>$cliente->id,
                'nombre'=>$data['nombre'],
                'apellido_paterno'=>$data['apellido_paterno'],
                'apellido_materno'=>$data['apellido_materno'],
                'email'=>$data['email'],
                'telefono'=>$data['telefono'],
                'ciudad'=>$data['ciudad'],
                'tipo_proceso'=>$data['tipo_proceso'],
                'descripcion'=>$data['descripcion'],
                'origen'=>$data['origen'],
                'forma_ingreso'=>$data['forma_ingreso'],
                'colegio_u_otro'=>$data['colegio_u_otro'],

            ]);

            Cita::create([

                'cliente_id'=>$cliente->id,

                'consulta_id'=>$consulta->id,

                'fecha_hora'=>$data['fecha_hora'],

                'modalidad'=>$data['modalidad'],

                'estado'=>$data['estado'],

                'notas'=>$data['notas'],

            ]);

            return $consulta;

        });
    }
}
