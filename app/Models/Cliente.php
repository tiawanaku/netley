<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombres',
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
        'correo',
        'numero_contrato',
        'estado',
        'foto',
        'nota_netley',
        'fecha_de_inicio',
        'es_preferente',
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
