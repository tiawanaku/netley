<?php

namespace Tests\Feature;

use App\Models\Caso;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ContratoPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_descarga_el_contrato_en_pdf(): void
    {
        $abogado = User::factory()->create(['name' => 'Abogado de Prueba']);
        $user = User::factory()->create();
        $user->givePermissionTo(
            Permission::firstOrCreate(['name' => 'View:Caso', 'guard_name' => 'web']),
        );
        $this->actingAs($user);

        $cliente = Cliente::create([
            'nombres' => 'Ana', 'apellido_paterno' => 'Perez', 'ci' => '1234567',
            'correo' => 'ana@example.com', 'telefono' => '70000000',
        ]);

        $caso = Caso::create([
            'cliente_id' => $cliente->id,
            'abogado_id' => $abogado->id,
            'especialidad' => 'Derecho de Familia',
            'tipo' => 'Divorcio',
            'fecha_inicio' => now(),
            'tiempo_proceso_meses' => 4,
            'monto_iguala_profesional' => 9000,
        ]);

        Pago::create([
            'cliente_id' => $cliente->id,
            'numero_recibo' => 'REC-0001',
            'monto' => 3000,
            'fecha_pago' => now(),
        ]);

        $response = $this->get(route('casos.contrato', $caso));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
