<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caso extends Model
{
    use SoftDeletes;

    /**
     * CU-012 Crear Workflow Procesal: cada materia posee su propia plantilla (RN-030).
     * Las materias no listadas usan la plantilla "Genérico".
     */
    protected const PLANTILLAS_WORKFLOW = [
        'Divorcio' => ['Presentación', 'Admisión', 'Citación', 'Contestación', 'Audiencia', 'Sentencia'],
        'Penal' => ['Denuncia', 'Investigación', 'Imputación', 'Acusación', 'Juicio', 'Sentencia'],
        'Laboral' => ['Demanda', 'Conciliación', 'Contestación', 'Audiencia', 'Sentencia'],
        'Genérico' => ['Presentación', 'Admisión', 'Trámite', 'Resolución'],
    ];

    protected $fillable = [
        'codigo',
        'cliente_id',
        'abogado_id',
        'procurador_id',
        'especialidad',
        'tipo',
        'juzgado',
        'estado',
        'prioridad',
        'fecha_inicio',
        'fecha_fin',
        // CU-010 Completar Ficha del Caso
        'juez',
        'secretario',
        'fiscal',
        'demandante',
        'demandado',
        'numero_expediente',
        'nurej',
        'delito',
        'materia',
        'tribunal',
        'telefonos',
        'correos',
        'direccion',
        'observaciones',
        // Datos del proceso (registrados desde "Reuniones Agendadas" al abrir el caso)
        'tiempo_proceso_meses',
        'requiere_poder',
        'monto_iguala_profesional',
        'monto_comision',
        'comision_porcentaje',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'requiere_poder' => 'boolean',
            'monto_iguala_profesional' => 'decimal:2',
            'monto_comision' => 'decimal:2',
            'comision_porcentaje' => 'decimal:2',
        ];
    }

    /**
     * CU-009 Crear Caso Jurídico: código autogenerado si no se especifica uno.
     */
    protected static function booted(): void
    {
        static::created(function (Caso $caso) {
            if (! $caso->codigo) {
                $caso->updateQuietly([
                    'codigo' => 'CASO-'.$caso->fecha_inicio->format('Y').'-'.str_pad($caso->id, 4, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function abogado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }

    public function procurador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'procurador_id');
    }

    /**
     * CU-011 Asignar Equipo Jurídico: colaboradores adicionales (pasante, psicólogo, conciliador...).
     * RN-020 (abogado patrocinador) y el procurador principal ya son columnas directas de `casos`.
     */
    public function equipo(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'caso_equipo')
            ->withPivot('rol')
            ->withTimestamps();
    }

    public function workflowEtapas(): HasMany
    {
        return $this->hasMany(WorkflowEtapa::class)->orderBy('orden');
    }

    public function actuaciones(): HasMany
    {
        return $this->hasMany(Actuacion::class);
    }

    public function observaciones(): HasMany
    {
        return $this->hasMany(Observacion::class);
    }

    public function hitos(): HasMany
    {
        return $this->hasMany(Hito::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    public function solicitudesDocumento(): HasMany
    {
        return $this->hasMany(SolicitudDocumento::class);
    }

    public function cuotasPago(): HasMany
    {
        return $this->hasMany(CuotaPago::class)->orderBy('numero');
    }

    public function audiencias(): HasMany
    {
        return $this->hasMany(Audiencia::class);
    }

    public function conciliaciones(): HasMany
    {
        return $this->hasMany(Conciliacion::class);
    }

    public function diligencias(): HasMany
    {
        return $this->hasMany(Diligencia::class);
    }

    public function medidasCautelares(): HasMany
    {
        return $this->hasMany(MedidaCautelar::class);
    }

    public function resolucionesJudiciales(): HasMany
    {
        return $this->hasMany(ResolucionJudicial::class);
    }

    /**
     * CU-012: genera automáticamente las etapas del workflow según la materia del caso.
     * RN-032: las etapas originales no pueden eliminarse (se marcan es_original=true).
     */
    public function generarWorkflow(): void
    {
        if ($this->workflowEtapas()->where('es_original', true)->exists()) {
            return;
        }

        $plantilla = self::PLANTILLAS_WORKFLOW[$this->materia] ?? self::PLANTILLAS_WORKFLOW['Genérico'];

        foreach ($plantilla as $indice => $nombreEtapa) {
            $this->workflowEtapas()->create([
                'nombre' => $nombreEtapa,
                'orden' => $indice + 1,
                'fecha_estimada' => $this->fecha_inicio->copy()->addDays(15 * ($indice + 1)),
                'es_original' => true,
            ]);
        }
    }

    /**
     * CU-025 Cerrar Caso: devuelve los motivos que impiden el cierre (vacío = se puede cerrar).
     *
     * NOTA: la validación de "pagos pendientes" y "tareas abiertas" del CU no está implementada
     * todavía porque los dominios de Finanzas (planes de pago) y Tareas no existen en el sistema aún.
     */
    public function motivosQueImpidenCierre(): array
    {
        $motivos = [];

        if ($this->audiencias()->where('fecha', '>=', now()->toDateString())->exists()) {
            $motivos[] = 'Existen audiencias futuras.';
        }

        if ($this->workflowEtapas()->where('es_original', true)->where('completada', false)->exists()) {
            $motivos[] = 'Existe workflow sin completar.';
        }

        if ($this->solicitudesDocumento()->where('estado', 'Pendiente')->exists()) {
            $motivos[] = 'Existen documentos pendientes.';
        }

        return $motivos;
    }

    public function cerrar(): void
    {
        $this->update([
            'estado' => 'Cerrado',
            'fecha_fin' => now(),
        ]);
    }
}
