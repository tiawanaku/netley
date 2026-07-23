<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'cliente_id',
        'consulta_id',
        'numero_recibo',
        'monto',
        'fecha_pago',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_pago' => 'datetime',
        ];
    }

    /**
     * CU-006 Registrar Pago Inicial: "Sin pago no existe Cliente Preferente".
     * El pago inicial es lo que convierte al interesado en Cliente Ejecutivo (antes "Cliente Preferente").
     *
     * TODO CU-007: falta "crear credenciales / habilitar Portal Cliente" — depende del Dominio 5
     * (Portal Cliente), que todavía no existe como panel propio. No se genera un User aquí porque
     * hoy eso daría acceso al panel de staff (canAccessPanel siempre true), lo cual sería un hueco
     * de seguridad hasta que exista un guard/panel separado para clientes.
     */
    protected static function booted(): void
    {
        static::created(function (Pago $pago) {
            $cliente = $pago->cliente;

            $cliente->update([
                'es_preferente' => true,
                'fecha_de_inicio' => $cliente->fecha_de_inicio ?? $pago->fecha_pago,
            ]);
        });
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }
}
