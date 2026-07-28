<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Contrato de Prestación de Servicios - {{ $cliente->nombre_completo }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1.5; color: #111; }
        h1 { text-align: center; font-size: 14px; margin-bottom: 24px; }
        p { text-align: justify; margin: 0 0 10px; }
        .clausula-titulo { font-weight: bold; }
        .firmas { margin-top: 60px; width: 100%; }
        .firmas td { width: 50%; text-align: center; padding-top: 40px; border-top: 1px solid #111; font-size: 10px; }
        .firmas-row td { border-top: none; padding-top: 0; }
    </style>
</head>
<body>
    <h1>CONTRATO DE PRESTACIÓN DE SERVICIOS - IGUALA PROFESIONAL</h1>

    <p>
        Conste por la presente Iguala Profesional, que a solo reconocimiento de firmas y rúbricas
        será elevado a instrumento público, al tenor de las siguientes cláusulas:
    </p>

    <p>
        <span class="clausula-titulo">PRIMERA.- (PARTES):</span>
        Son partes contratantes, la empresa Netley S.R.L. representada por
        {{ $abogado->name ?? '________________________________' }},
        con C.I. {{ $abogadoCi ?? '____________' }}, mayor de edad, hábil por derecho, quien en lo
        posterior a efectos de redacción se denominará EL ABOGADO; y por otra parte, interviene en
        la suscripción de la presente Iguala {{ $cliente->nombre_completo }},
        con C.I. {{ $cliente->ci ?? '____________' }}, mayor de edad y hábil por derecho, que en
        adelante se denominará EL CLIENTE.
    </p>

    <p>
        <span class="clausula-titulo">SEGUNDA.- (OBJETO):</span>
        EL CLIENTE, en forma libre y voluntaria y sin que medie vicio alguno del consentimiento,
        contrata los servicios profesionales del ABOGADO, para {{ $objeto ?: '________________________________' }}.
    </p>

    <p>
        <span class="clausula-titulo">TERCERA.- (OBLIGACIONES DE LAS PARTES):</span>
        Son obligaciones de las partes las siguientes:
    </p>
    <p>
        EL CLIENTE se obliga a: proporcionar al ABOGADO, para su adecuado patrocinio legal, toda la
        información y documentación que sea necesaria y que obre en su poder para una adecuada
        conducción legal del proceso, bajo responsabilidad; presentar la documentación y las pruebas
        en el plazo solicitado por el ABOGADO, al efecto la comunicación será por vía telefónica o
        cualquier medio de comunicación; mantener en reserva toda la información proporcionada por
        el ABOGADO; cancelar puntualmente los honorarios profesionales acordados; y correr con todos
        los gastos emergentes del proceso, como notificación, fotocopias, transporte, valores
        judiciales y de ser necesario viáticos.
    </p>
    <p>
        EL ABOGADO se obliga a: realizar el servicio encomendado por el presente contrato;
        proporcionar un patrocinio profesional, idóneo y ético al CLIENTE; mantener reserva sobre
        toda la información proporcionada por EL CLIENTE; mantener informado al CLIENTE sobre el
        avance y el estado de la causa verbalmente y por escrito cuando sea requerido dicha
        información; asesorar técnicamente al CLIENTE hasta la conclusión del contrato; y finalizar
        el servicio encomendado en un lapso de tiempo de {{ $caso->tiempo_proceso_meses ?? '____' }}
        meses, salvo que exista demora por causa a) del cliente, b) del órgano judicial, o c) fuerza
        mayor.
    </p>

    <p>
        <span class="clausula-titulo">CUARTA.- (HONORARIOS PROFESIONALES Y FORMA DE PAGO):</span>
        EL CLIENTE se obliga a cancelar a favor del ABOGADO la suma de
        Bs. {{ number_format((float) $caso->monto_iguala_profesional, 2) }}, monto que corresponde al
        pago por el servicio descrito en la cláusula segunda, mismos que serán cubiertos de la
        siguiente manera:
    </p>
    <p>
        Bs. {{ number_format((float) $anticipo, 2) }}, a la firma del presente documento.<br>
        Bs. {{ number_format((float) $saldo, 2) }}, mismo que puede ser cancelado según avanza su
        proceso e indispensablemente antes de que finalice el proceso.
    </p>
    <p>
        Se establece que la única forma de acreditar pagos de honorarios profesionales es a través
        de recibos emitidos por la empresa NETLEY S.R.L., mismos que constituyen prueba plena de
        cancelación.
    </p>

    <p>
        <span class="clausula-titulo">QUINTA.- (CONCLUSIÓN EXTRAORDINARIA):</span>
        Se deja expresamente establecido que los honorarios profesionales descritos en la cláusula
        cuarta deberán, sin excepción, ser cancelados a la empresa NETLEY S.R.L. de igual forma, si
        EL CLIENTE: a) por decisión propia desiste o abandona el caso, b) decide transar o
        conciliar, o c) decide contratar los servicios de otro abogado.
    </p>

    <p>
        <span class="clausula-titulo">SEXTA.- (CONFORMIDAD):</span>
        Ambas partes, EL ABOGADO y EL CLIENTE, manifestamos nuestra entera conformidad con todas y
        cada una de las cláusulas precedentes y nos obligamos a su fiel y estricto cumplimiento, a
        los {{ $fecha->translatedFormat('d') }} días del mes de {{ $fecha->translatedFormat('F') }}
        del año {{ $fecha->translatedFormat('Y') }}.
    </p>

    <table class="firmas">
        <tr class="firmas-row"><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr>
            <td>EL ABOGADO</td>
            <td>EL CLIENTE</td>
        </tr>
    </table>
</body>
</html>
