<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombres',          // ← Nota: es 'nombres' (plural)
        'apellido_paterno',
        'apellido_materno',
        'ci',
        'genero',
        'fecha_nacimiento',
        'nacionalidad',
        'estado_civil',
        'profesion',
        'direccion',
        'ciudad',
        'telefono',
        'whatsapp',
        'correo',           // ← La migración muestra 'correo'
        'numero_contrato',
        'estado',
        'foto',
        'nota_netley',
        'fecha_de_inicio',
        'es_preferente',
        'rol_empresa',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'es_preferente' => 'boolean',
            'fecha_nacimiento' => 'date',
            'fecha_de_inicio' => 'date',
        ];
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * true cuando esta persona tiene un rol asignado dentro de la empresa (Abogado, Psicólogo,
     * Procurador, Trabajo Social, Pasante, Otros) pero todavía no tiene usuario para entrar al panel.
     */
    public function necesitaAccesoStaff(): bool
    {
        return filled($this->rol_empresa) && is_null($this->user_id);
    }

    /**
     * Genera el usuario y una contraseña temporal para el personal (Abogado/Psicólogo/etc.),
     * asignándole el rol de Filament Shield correspondiente. Devuelve la contraseña en texto plano
     * para que quien lo cree se la pueda compartir (una sola vez, no se puede recuperar después).
     *
     * A diferencia del Cliente Ejecutivo (CU-006/007), aquí SÍ corresponde dar acceso al panel:
     * el personal trabaja en el sistema, el cliente no (eso es el Portal Cliente, Dominio 5).
     */
    public function generarAccesoStaff(): string
    {
        $passwordTemporal = Str::password(10, symbols: false);

        $user = User::create([
            'name' => $this->nombre_completo,
            'email' => $this->correo,
            'password' => $passwordTemporal,
        ]);

        $rol = Role::firstOrCreate(['name' => $this->rol_empresa, 'guard_name' => 'web']);
        $user->assignRole($rol);

        $this->updateQuietly(['user_id' => $user->id]);

        return $passwordTemporal;
    }

    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function casos(): HasMany
    {
        return $this->hasMany(Caso::class);
    }
}