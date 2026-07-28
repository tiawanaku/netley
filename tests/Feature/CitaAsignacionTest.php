<?php

namespace Tests\Feature;

use App\Filament\Resources\Citas\Pages\EditCita;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CitaAsignacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_asignar_una_cita_existente_a_un_miembro_del_personal(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo(
            Permission::firstOrCreate(['name' => 'ViewAny:Cita', 'guard_name' => 'web']),
            Permission::firstOrCreate(['name' => 'View:Cita', 'guard_name' => 'web']),
            Permission::firstOrCreate(['name' => 'Update:Cita', 'guard_name' => 'web']),
        );
        $this->actingAs($admin);

        $abogado = User::factory()->create(['name' => 'Abogado Uno']);

        $cliente = Cliente::create([
            'nombres' => 'Debug',
            'apellido_paterno' => 'Test',
            'correo' => 'debug-asignacion@example.com',
            'telefono' => '70000000',
        ]);

        // fecha_hora intencionalmente fuera del horario de atención (8am-6pm) y sin caer en un
        // múltiplo de 15 minutos, tal como quedan las citas registradas antes de esta funcionalidad.
        $cita = Cita::create([
            'cliente_id' => $cliente->id,
            'fecha_hora' => '2026-07-27 19:53:37',
            'modalidad' => 'Presencial',
            'estado' => 'Pendiente',
        ]);

        Livewire::test(EditCita::class, ['record' => $cita->getRouteKey()])
            ->fillForm(['asignado_a_user_id' => $abogado->id])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($abogado->id, $cita->fresh()->asignado_a_user_id);
        // La hora original no debe alterarse solo por abrir/guardar el formulario.
        $this->assertSame('2026-07-27 19:53:37', $cita->fresh()->fecha_hora->format('Y-m-d H:i:s'));
    }
}
