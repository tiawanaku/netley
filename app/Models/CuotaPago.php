<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Una cuota del plan de pagos de un Caso. Cada cuota representa un mes; la cantidad de cuotas
 * no debe exceder Caso::tiempo_proceso_meses (se valida en el formulario, ver ResultadoCitaForm).
 */
class CuotaPago extends Model
{
    protected $table = 'cuotas_pago';

    protected $fillable = [
        'caso_id',
        'numero',
        'monto',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
        ];
    }

    public function caso(): BelongsTo
    {
        return $this->belongsTo(Caso::class);
    }
}
