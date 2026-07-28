<?php

namespace Tests\Feature;

use App\Filament\Pages\ReunionesAgendadas;
use App\Models\Caso;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ReunionesAgendadasTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAbogado(): User
    {
        $abogado = User::factory()->create();
        $abogado->givePermissionTo(
            Permission::firstOrCreate(['name' => 'View:ReunionesAgendadas', 'guard_name' => 'web']),
            Permission::firstOrCreate(['name' => 'Update:Cita', 'guard_name' => 'web']),
        );
        $this->actingAs($abogado);

        return $abogado;
    }

    public function test_el_personal_solo_ve_las_citas_que_se_le_asignaron(): void
    {
        $abogado = $this->actingAsAbogado();
        $otroStaff = User::factory()->create();

        $cliente = Cliente::create([
            'nombres' => 'Debug',
            'apellido_paterno' => 'Test',
            'correo' => 'debug-reunion@example.com',
            'telefono' => '70000000',
        ]);

        $citaAsignada = Cita::create([
            'cliente_id' => $cliente->id,
            'asignado_a_user_id' => $abogado->id,
            'fecha_hora' => '2026-08-13 08:15:00',
            'modalidad' => 'Presencial',
            'estado' => 'Pendiente',
        ]);

        $citaDeOtro = Cita::create([
            'cliente_id' => $cliente->id,
            'asignado_a_user_id' => $otroStaff->id,
            'fecha_hora' => '2026-08-13 09:00:00',
            'modalidad' => 'Virtual',
            'estado' => 'Pendiente',
        ]);

        Livewire::test(ReunionesAgendadas::class)
            ->assertCanSeeTableRecords([$citaAsignada])
            ->assertCanNotSeeTableRecords([$citaDeOtro]);
    }

    public function test_registrar_resultado_actualiza_cliente_y_crea_caso_pago_cuotas_y_documentos(): void
    {
        Storage::fake('public');

        $abogado2 = User::factory()->create(['name' => 'Procurador Uno']);
        $abogado = $this->actingAsAbogado();

        $cliente = Cliente::create([
            'nombres' => 'Debug',
            'apellido_paterno' => 'Viejo',
            'correo' => 'debug-resultado@example.com',
            'telefono' => '70000000',
        ]);

        $cita = Cita::create([
            'cliente_id' => $cliente->id,
            'asignado_a_user_id' => $abogado->id,
            'fecha_hora' => '2026-08-13 08:15:00',
            'modalidad' => 'Presencial',
            'estado' => 'Pendiente',
        ]);

        Livewire::test(ReunionesAgendadas::class)
            ->callTableAction('registrarResultado', $cita, data: [
                'determinacion' => ['Ejecutivo', 'Cerrar Caso'],
                'cliente_nombres' => 'Debug Actualizado',
                'cliente_apellido_paterno' => 'Nuevo',
                'cliente_telefono' => '71111111',
                'hora_inicio_reunion' => '08:15',
                'hora_fin_reunion' => '09:00',
                'area' => 'Derecho de Familia',
                'tipo_proceso' => 'Divorcio',
                'abogado1_id' => $abogado->id,
                'abogado2_id' => $abogado2->id,
                'tiempo_proceso_meses' => 3,
                'requiere_poder' => true,
                'monto_iguala_profesional' => 9000,
                'monto_comision' => 500,
                'comision_porcentaje' => 10,
                'anticipo' => 3000,
                'numero_recibo' => 'REC-0001',
                'cuotas' => [
                    ['monto' => 3000],
                    ['monto' => 3000],
                ],
                'documentos' => [
                    ['archivo' => [UploadedFile::fake()->create('carnet.pdf', 100)], 'formato' => 'Original'],
                ],
            ])
            ->assertHasNoTableActionErrors();

        $cliente->refresh();
        $this->assertSame('Debug Actualizado', $cliente->nombres);
        $this->assertSame('Nuevo', $cliente->apellido_paterno);
        $this->assertSame('71111111', $cliente->telefono);

        $cita->refresh();
        $this->assertSame(['Ejecutivo', 'Cerrar Caso'], $cita->determinacion);
        $this->assertSame('08:15', $cita->hora_inicio_reunion);

        $caso = Caso::where('cliente_id', $cliente->id)->first();
        $this->assertNotNull($caso, 'Debió crearse un Caso.');
        $this->assertSame('Derecho de Familia', $caso->especialidad);
        $this->assertSame($abogado->id, $caso->abogado_id);
        $this->assertSame($abogado2->id, $caso->procurador_id);
        $this->assertSame(3, $caso->tiempo_proceso_meses);
        $this->assertTrue($caso->requiere_poder);
        $this->assertSame(2, $caso->cuotasPago()->count());
        $this->assertSame(1, $caso->documentos()->count());
        $this->assertSame('Original', $caso->documentos()->first()->formato);

        $pago = Pago::where('cliente_id', $cliente->id)->first();
        $this->assertNotNull($pago, 'Debió crearse un Pago (anticipo).');
        $this->assertSame('REC-0001', $pago->numero_recibo);
        $this->assertTrue($cliente->fresh()->es_preferente);
    }
}
