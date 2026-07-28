<?php

namespace Tests\Feature;

use App\Filament\Resources\Consultas\Pages\CreateConsulta;
use App\Models\Cliente;
use App\Models\Consulta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ConsultaWizardTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUsuarioConPermiso(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            Permission::firstOrCreate(['name' => 'Create:Consulta', 'guard_name' => 'web']),
            Permission::firstOrCreate(['name' => 'ViewAny:Consulta', 'guard_name' => 'web']),
        );
        $this->actingAs($user);

        return $user;
    }

    public function test_wizard_crea_cliente_consulta_y_cita_cuando_el_cliente_no_existe(): void
    {
        $this->actingAsUsuarioConPermiso();

        $this->assertSame(0, Cliente::where('correo', 'ana.wizardtest@example.com')->count());

        Livewire::test(CreateConsulta::class)
            ->fillForm([
                'nombre' => 'Ana',
                'apellido_paterno' => 'Rojas',
                'apellido_materno' => 'Diaz',
                'email' => 'ana.wizardtest@example.com',
                'telefono' => '71111111',
                'ciudad' => 'Cochabamba',
                'tipo_proceso' => 'Laboral',
                'descripcion' => 'Prueba wizard',
                'origen' => 'landing',
                'forma_ingreso' => 'Netley',
                'colegio_u_otro' => null,
                'cita_fecha' => now()->format('Y-m-d'),
                'cita_hora' => '08:00',
                'modalidad' => 'Presencial',
                'estado' => 'Pendiente',
                'notas' => 'Nota de prueba',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $cliente = Cliente::where('correo', 'ana.wizardtest@example.com')->first();
        $consulta = Consulta::where('email', 'ana.wizardtest@example.com')->first();

        $this->assertNotNull($cliente, 'El cliente debió crearse automáticamente.');
        $this->assertNotNull($consulta);
        $this->assertSame($cliente->id, $consulta->cliente_id);
        $this->assertSame('Rojas', $consulta->apellido_paterno);
        $this->assertSame(1, $cliente->citas()->count());
        $this->assertNotNull($consulta->ticket);
    }

    public function test_wizard_reutiliza_cliente_existente_por_correo(): void
    {
        $this->actingAsUsuarioConPermiso();

        $clienteExistente = Cliente::create([
            'nombres' => 'Pedro',
            'apellido_paterno' => 'Mamani',
            'apellido_materno' => 'Choque',
            'correo' => 'pedro.existente@example.com',
            'telefono' => '70000000',
        ]);

        Livewire::test(CreateConsulta::class)
            ->fillForm([
                'nombre' => 'Pedro',
                'apellido_paterno' => 'Mamani',
                'apellido_materno' => 'Choque',
                'email' => 'pedro.existente@example.com',
                'telefono' => '70000000',
                'ciudad' => 'La Paz',
                'tipo_proceso' => 'Divorcio',
                'descripcion' => 'Segunda consulta del mismo cliente',
                'origen' => 'landing',
                'forma_ingreso' => 'Netley',
                'colegio_u_otro' => null,
                'cita_fecha' => now()->format('Y-m-d'),
                'cita_hora' => '08:00',
                'modalidad' => 'Virtual',
                'estado' => 'Pendiente',
                'notas' => null,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(1, Cliente::where('correo', 'pedro.existente@example.com')->count());

        $consulta = Consulta::where('email', 'pedro.existente@example.com')->first();
        $this->assertSame($clienteExistente->id, $consulta->cliente_id);
    }
}
