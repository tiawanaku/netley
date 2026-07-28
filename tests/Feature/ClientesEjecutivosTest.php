<?php

namespace Tests\Feature;

use App\Filament\Pages\ClientesEjecutivos;
use App\Models\Caso;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ClientesEjecutivosTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAbogado(): User
    {
        $abogado = User::factory()->create();
        $abogado->givePermissionTo(
            Permission::firstOrCreate(['name' => 'View:ClientesEjecutivos', 'guard_name' => 'web']),
        );
        $this->actingAs($abogado);

        return $abogado;
    }

    public function test_solo_ve_clientes_ejecutivos_con_caso_asignado_a_este_profesional(): void
    {
        $abogado = $this->actingAsAbogado();
        $otroAbogado = User::factory()->create();

        $clientePropio = Cliente::create([
            'nombres' => 'Ana', 'apellido_paterno' => 'Perez', 'correo' => 'ana@example.com',
            'telefono' => '70000000', 'es_preferente' => true,
        ]);
        Caso::create([
            'cliente_id' => $clientePropio->id, 'abogado_id' => $abogado->id,
            'especialidad' => 'Familia', 'fecha_inicio' => now(),
        ]);

        $clienteDeOtro = Cliente::create([
            'nombres' => 'Beto', 'apellido_paterno' => 'Gomez', 'correo' => 'beto@example.com',
            'telefono' => '70000001', 'es_preferente' => true,
        ]);
        Caso::create([
            'cliente_id' => $clienteDeOtro->id, 'abogado_id' => $otroAbogado->id,
            'especialidad' => 'Penal', 'fecha_inicio' => now(),
        ]);

        $clienteSinPago = Cliente::create([
            'nombres' => 'Carla', 'apellido_paterno' => 'Ruiz', 'correo' => 'carla@example.com',
            'telefono' => '70000002', 'es_preferente' => false,
        ]);
        Caso::create([
            'cliente_id' => $clienteSinPago->id, 'abogado_id' => $abogado->id,
            'especialidad' => 'Laboral', 'fecha_inicio' => now(),
        ]);

        Livewire::test(ClientesEjecutivos::class)
            ->assertCanSeeTableRecords([$clientePropio])
            ->assertCanNotSeeTableRecords([$clienteDeOtro, $clienteSinPago]);
    }

    public function test_puede_subir_un_documento_al_expediente_del_cliente(): void
    {
        Storage::fake('public');

        $abogado = $this->actingAsAbogado();

        $cliente = Cliente::create([
            'nombres' => 'Ana', 'apellido_paterno' => 'Perez', 'correo' => 'ana2@example.com',
            'telefono' => '70000000', 'es_preferente' => true,
        ]);
        $caso = Caso::create([
            'cliente_id' => $cliente->id, 'abogado_id' => $abogado->id,
            'especialidad' => 'Familia', 'fecha_inicio' => now(),
        ]);

        Livewire::test(ClientesEjecutivos::class)
            ->callTableAction('verExpediente', $cliente, data: [
                'documentos' => [
                    [
                        'caso_id' => $caso->id,
                        'archivo' => [UploadedFile::fake()->create('contrato-firmado.pdf', 200)],
                        'tipo' => 'Contrato',
                        'formato' => 'Original',
                    ],
                ],
            ])
            ->assertHasNoTableActionErrors();

        $documento = Documento::where('caso_id', $caso->id)->first();
        $this->assertNotNull($documento);
        $this->assertSame('Contrato', $documento->tipo);
        $this->assertSame('Original', $documento->formato);
        $this->assertSame($abogado->id, $documento->propietario_id);
    }
}
