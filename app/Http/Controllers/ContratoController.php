<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ContratoController extends Controller
{
    /**
     * Genera el "Contrato de Prestación de Servicios - Iguala Profesional" (basado en la
     * plantilla documentos/contrato_netley.docx) con los datos del Caso, listo para que el
     * cliente lo firme. Una vez firmado, el escaneado se sube como Documento (tipo Contrato)
     * desde "Clientes Ejecutivos" > Expediente.
     */
    public function descargar(Caso $caso): Response
    {
        Gate::authorize('view', $caso);

        $cliente = $caso->cliente;
        $abogado = $caso->abogado;

        $anticipo = (float) (Pago::query()
            ->where('cliente_id', $cliente->id)
            ->latest('fecha_pago')
            ->value('monto') ?? 0);

        $igualaProfesional = (float) ($caso->monto_iguala_profesional ?? 0);
        $saldo = max($igualaProfesional - $anticipo, 0);
        $objeto = trim(collect([$caso->especialidad, $caso->tipo])->filter()->implode(' - '));

        $pdf = Pdf::loadView('pdf.contrato', [
            'caso' => $caso,
            'cliente' => $cliente,
            'abogado' => $abogado,
            'abogadoCi' => null,
            'objeto' => $objeto,
            'anticipo' => $anticipo,
            'saldo' => $saldo,
            'fecha' => $caso->fecha_inicio ?? now(),
        ]);

        return $pdf->download('contrato_netley_'.Str::slug($cliente->nombre_completo).'.pdf');
    }
}
