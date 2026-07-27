<?php

namespace App\Filament\Resources\Consultas\Pages;

use App\Models\Consulta;
use App\Models\Cita;
use App\Filament\Resources\Consultas\ConsultaResource;
use App\Filament\Resources\Consultas\Schemas\ConsultaForm;
use App\Filament\Resources\Citas\Schemas\CitaForm;

use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\DB;

class CreateConsulta extends CreateRecord
{
    protected static string $resource = ConsultaResource::class;

    protected function getFormSchema(): array
    {
        return [

            Wizard::make([

                Step::make('Consulta')
                    ->schema(
                        ConsultaForm::components()
                    ),

                Step::make('Cita')
                    ->schema(
                        CitaForm::components()
                    ),

            ])

            ->columnSpanFull()

        ];
    }

    protected function handleRecordCreation(array $data): Consulta
    {
        return DB::transaction(function () use ($data) {

            $consulta = Consulta::create([

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