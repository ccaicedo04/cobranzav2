<?php

namespace App\Services;

use App\Models\ConceptoModel;
use App\Models\DeudaModel;
use App\Models\EstudianteModel;
use App\Models\ResponsableModel;
use App\Models\SedeModel;
use Shuchkin\SimpleXLSX;

require_once __DIR__ . '/../../core/SimpleXlsxReader.php';

class CargaPhidiasService
{
    /** @var ResponsableModel */
    private $responsables;
    /** @var EstudianteModel */
    private $estudiantes;
    /** @var ConceptoModel */
    private $conceptos;
    /** @var DeudaModel */
    private $deudas;
    /** @var SedeModel */
    private $sedes;
    /** @var array<int,array<int,int>> */
    private $sedesCache = [];

    public function __construct()
    {
        $this->responsables = new ResponsableModel();
        $this->estudiantes = new EstudianteModel();
        $this->conceptos = new ConceptoModel();
        $this->deudas = new DeudaModel();
        $this->sedes = new SedeModel();
    }

    /**
     * Procesa un archivo XLSX con la estructura exportada por Phidias.
     *
     * @return array{procesados:int,deudas:int,responsables:int,estudiantes:int,errores:array<int,array{fila:int,mensaje:string}>}
     */
    public function procesar(string $rutaTemporal, int $idColegio, int $anio, bool $guardar = true): array
    {
        $xlsx = SimpleXLSX::parse($rutaTemporal);
        if (!$xlsx) {
            $contenido = @file_get_contents($rutaTemporal);
            if ($contenido !== false) {
                $xlsx = SimpleXLSX::parseData($contenido);
            }
        }
        if (!$xlsx) {
            $detalle = SimpleXLSX::parseError();
            $mensaje = 'No se pudo leer el archivo XLSX proporcionado.';
            if ($detalle) {
                $mensaje .= ' Detalle: ' . $detalle;
            }
            throw new \RuntimeException($mensaje);
        }

        $sheetIndex = $this->buscarIndiceHoja($xlsx, 'Hoja1');

        $rows = $xlsx->rows($sheetIndex);
        if (count($rows) < 3) {
            throw new \RuntimeException('El archivo no contiene datos suficientes.');
        }

        $indiceEncabezado = $this->buscarFilaEncabezado($rows);
        if ($indiceEncabezado === null) {
            throw new \RuntimeException('No se encontró la fila de encabezado con "Etiquetas de fila".');
        }

        $this->validarEncabezado($rows[$indiceEncabezado] ?? []);

        $errores = [];
        $responsablesProcesados = 0;
        $estudiantesProcesados = 0;
        $deudasRegistradas = 0;
        $valorTotal = 0.0;
        $totalesMes = [];
        $filaExcel = $indiceEncabezado + 2; // Excel es 1-based

        $responsablesVistos = [];
        $estudiantesVistos = [];

        $contexto = [
            'id_responsable' => null,
            'id_estudiante' => null,
            'responsable' => null,
            'estudiante' => null,
            'tiene_detalle' => false,
            'cabecera_montos' => [],
        ];

        for ($i = $indiceEncabezado + 1; $i < count($rows); $i++, $filaExcel++) {
            $fila = $rows[$i];
            $colA = $this->limpiarTexto($fila[0] ?? '');
            $colB = $this->limpiarTexto($fila[1] ?? '');
            $colC = $this->limpiarTexto($fila[2] ?? '');
            $colD = $this->limpiarTexto($fila[3] ?? '');
            $colE = $this->limpiarTexto($fila[4] ?? '');
            $colF = $this->limpiarTexto($fila[5] ?? '');

            if ($colA !== '' && $colF !== '') {
                $this->finalizarCabeceraSinDetalle($contexto, $idColegio, $anio, $deudasRegistradas, $valorTotal, $totalesMes, $guardar);

                if ($colE === '') {
                    $errores[] = ['fila' => $filaExcel, 'mensaje' => 'Cabecera inválida: falta código de alumno'];
                    $contexto = [
                        'id_responsable' => null,
                        'id_estudiante' => null,
                        'responsable' => null,
                        'estudiante' => null,
                        'tiene_detalle' => false,
                        'cabecera_montos' => [],
                    ];
                    continue;
                }

                $codigoAlumno = $colE;
                if ($colB === '' || $colF === '') {
                    $errores[] = ['fila' => $filaExcel, 'mensaje' => 'Cabecera inválida: faltan nombres'];
                    $contexto = [
                        'id_responsable' => null,
                        'id_estudiante' => null,
                        'responsable' => null,
                        'estudiante' => null,
                        'tiene_detalle' => false,
                        'cabecera_montos' => [],
                    ];
                    continue;
                }

                $sedeDestino = $this->resolverSedePorCodigo($idColegio, $codigoAlumno);
                $contexto['id_responsable'] = $this->upsertResponsable($idColegio, $sedeDestino, $colA, $colB, $colC, $colD, $guardar);
                $contexto['responsable'] = $colB;
                $contexto['id_estudiante'] = $this->upsertEstudiante($idColegio, $sedeDestino, $contexto['id_responsable'], $codigoAlumno, $colF, $guardar);
                $contexto['estudiante'] = $colF;
                $contexto['tiene_detalle'] = false;
                $contexto['cabecera_montos'] = $this->montosCabecera($fila);
                $contexto['id_sede'] = $sedeDestino;

                if (!in_array($colA, $responsablesVistos, true)) {
                    $responsablesVistos[] = $colA;
                    $responsablesProcesados++;
                }
                if (!in_array($codigoAlumno, $estudiantesVistos, true)) {
                    $estudiantesVistos[] = $codigoAlumno;
                    $estudiantesProcesados++;
                }

                continue;
            }

            if ($colA === '' && $colE !== '' && $colF !== '' && $contexto['id_responsable']) {
                $this->finalizarCabeceraSinDetalle($contexto, $idColegio, $anio, $deudasRegistradas, $valorTotal, $totalesMes, $guardar);

                $sedeDestino = $this->resolverSedePorCodigo($idColegio, $colE);
                $contexto['id_estudiante'] = $this->upsertEstudiante($idColegio, $sedeDestino, $contexto['id_responsable'], $colE, $colF, $guardar);
                $contexto['estudiante'] = $colF;
                $contexto['tiene_detalle'] = false;
                $contexto['cabecera_montos'] = $this->montosCabecera($fila);
                $contexto['id_sede'] = $sedeDestino;
                if (!in_array($colE, $estudiantesVistos, true)) {
                    $estudiantesVistos[] = $colE;
                    $estudiantesProcesados++;
                }
                continue;
            }

            if ($colA === '' && $colE !== '' && $colF !== '' && !$contexto['id_responsable']) {
                $errores[] = ['fila' => $filaExcel, 'mensaje' => 'Estudiante sin responsable previo'];
                continue;
            }

            if ($colA === '' && $colE === '' && $colF !== '' && $contexto['id_estudiante']) {
                [$deudasSumadas, $valorSumado, $totalesFila] = $this->procesarDetalleConcepto($fila, $contexto, $idColegio, $anio, $filaExcel, $guardar);
                $contexto['tiene_detalle'] = $contexto['tiene_detalle'] || $deudasSumadas > 0;
                $deudasRegistradas += $deudasSumadas;
                $valorTotal += $valorSumado;
                foreach ($totalesFila as $mes => $valor) {
                    if (!isset($totalesMes[$mes])) {
                        $totalesMes[$mes] = 0.0;
                    }
                    $totalesMes[$mes] += $valor;
                }
                continue;
            }

            if ($colA === '' && $colE === '' && $colF !== '' && !$contexto['id_estudiante']) {
                $errores[] = ['fila' => $filaExcel, 'mensaje' => 'Concepto sin cabecera previa'];
            }
        }

        $this->finalizarCabeceraSinDetalle($contexto, $idColegio, $anio, $deudasRegistradas, $valorTotal, $totalesMes, $guardar);

        return [
            'procesados' => $responsablesProcesados + $estudiantesProcesados,
            'deudas' => $deudasRegistradas,
            'valor_total' => $valorTotal,
            'responsables' => $responsablesProcesados,
            'estudiantes' => $estudiantesProcesados,
            'errores' => $errores,
            'totales_meses' => $totalesMes,
        ];
    }

    private function buscarFilaEncabezado(array $rows): ?int
    {
        foreach ($rows as $indice => $row) {
            $columnaA = $this->limpiarTexto($row[0] ?? '');
            if ($columnaA === 'Etiquetas de fila') {
                return $indice;
            }
        }

        return null;
    }

    private function validarEncabezado(array $encabezado): void
    {
        $esperado = [
            0 => 'Etiquetas de fila',
            1 => 'Nombre Responsable',
            2 => 'correo',
            3 => 'telefono',
            4 => 'Código del Alumno',
            5 => 'Alumno',
        ];

        foreach ($esperado as $indice => $texto) {
            $actual = $this->limpiarTexto($encabezado[$indice] ?? '');
            if ($actual !== $texto) {
                throw new \RuntimeException('El encabezado de la columna ' . ($indice + 1) . ' no coincide con la plantilla esperada.');
            }
        }
    }

    private function limpiarTexto($valor): string
    {
        $texto = str_replace("\xC2\xA0", ' ', (string) $valor); // elimina NBSP

        return trim($texto);
    }

    private function upsertResponsable(int $idColegio, int $idSede, string $documento, string $nombre, string $correo, string $telefono, bool $guardar): int
    {
        $existente = $this->responsables->all([
            'id_colegio' => $idColegio,
            'numero_documento' => $documento,
            'eliminado' => 0,
        ]);
        $payload = [
            'id_colegio' => $idColegio,
            'id_sede' => $idSede,
            'nombre_completo' => $nombre,
            'tipo_documento' => 'CC',
            'numero_documento' => $documento,
            'telefono' => $telefono,
            'correo' => $correo,
            'estado' => 'activo',
            'eliminado' => 0,
        ];

        if ($existente) {
            $id = (int) $existente[0]['id_responsable'];
            if ($guardar) {
                $this->responsables->update($id, array_filter($payload, static fn ($value) => $value !== '' && $value !== null));
            }
            return $id;
        }

        return $guardar ? $this->responsables->create($payload) : 1;
    }

    private function upsertEstudiante(int $idColegio, int $idSede, int $idResponsable, string $codigo, string $nombre, bool $guardar): int
    {
        $existente = $this->estudiantes->all([
            'id_colegio' => $idColegio,
            'codigo_estudiante' => $codigo,
            'eliminado' => 0,
        ]);

        $payload = [
            'id_colegio' => $idColegio,
            'id_sede' => $idSede,
            'id_responsable' => $idResponsable,
            'codigo_estudiante' => $codigo,
            'nombre_completo' => $nombre,
            'estado' => 'activo',
            'eliminado' => 0,
        ];

        if ($existente) {
            $id = (int) $existente[0]['id_estudiante'];
            if ($guardar) {
                $this->estudiantes->update($id, array_filter($payload, static fn ($value) => $value !== '' && $value !== null));
            }
            return $id;
        }

        return $guardar ? $this->estudiantes->create($payload) : 1;
    }

    private function procesarDetalleConcepto(array $fila, array $contexto, int $idColegio, int $anio, int $filaExcel, bool $guardar): array
    {
        $concepto = trim((string) ($fila[5] ?? ''));
        if ($concepto === '') {
            return [0, 0.0, []];
        }

        $meses = [6 => 7, 7 => 8, 8 => 9, 9 => 10];
        $totalDeudas = 0;
        $totalValor = 0.0;
        $totalesMes = [];

        foreach ($meses as $columna => $mes) {
            $valor = $this->normalizarNumero($fila[$columna] ?? null);
            if ($valor <= 0) {
                continue;
            }

            if ($guardar) {
                $this->registrarDeuda($idColegio, (int) ($contexto['id_sede'] ?? 0), $contexto['id_estudiante'], $concepto, $anio, $mes, $valor);
                $totalDeudas++;
            }
            $totalValor += $valor;
            $totalesMes[$mes] = ($totalesMes[$mes] ?? 0) + $valor;
        }

        $valorFormulario = $this->normalizarNumero($fila[10] ?? null);
        if ($valorFormulario > 0) {
            $conceptoFormulario = stripos($concepto, 'formulario') !== false ? $concepto : 'Formulario';
            if ($guardar) {
                $this->registrarDeuda($idColegio, (int) ($contexto['id_sede'] ?? 0), $contexto['id_estudiante'], $conceptoFormulario, $anio, 7, $valorFormulario);
                $totalDeudas++;
            }
            $totalValor += $valorFormulario;
            $totalesMes[7] = ($totalesMes[7] ?? 0) + $valorFormulario;
        }

        return [$totalDeudas, $totalValor, $totalesMes];
    }

    private function montosCabecera(array $fila): array
    {
        $meses = [6 => 7, 7 => 8, 8 => 9, 9 => 10];
        $montos = [];

        foreach ($meses as $columna => $mes) {
            $valor = $this->normalizarNumero($fila[$columna] ?? null);
            if ($valor > 0) {
                $montos[] = ['mes' => $mes, 'valor' => $valor];
            }
        }

        return $montos;
    }

    private function finalizarCabeceraSinDetalle(array &$contexto, int $idColegio, int $anio, int &$deudasRegistradas, float &$valorTotal, array &$totalesMes, bool $guardar): void
    {
        if (!$contexto['id_estudiante'] || $contexto['tiene_detalle'] || empty($contexto['cabecera_montos'])) {
            return;
        }

        foreach ($contexto['cabecera_montos'] as $monto) {
            if ($guardar) {
                $this->registrarDeuda($idColegio, (int) ($contexto['id_sede'] ?? 0), $contexto['id_estudiante'], 'Cartera sin desagregar', $anio, $monto['mes'], $monto['valor']);
                $deudasRegistradas++;
            }
            $valorTotal += $monto['valor'];
            $totalesMes[$monto['mes']] = ($totalesMes[$monto['mes']] ?? 0) + $monto['valor'];
        }

        $contexto['cabecera_montos'] = [];
    }

    private function resolverSedePorCodigo(int $idColegio, string $codigo): int
    {
        $codigo = preg_replace('/\D+/', '', $codigo);
        $longitud = strlen($codigo);
        $clave = $longitud >= 5 ? 'bogota' : 'cota';
        if (!isset($this->sedesCache[$idColegio])) {
            $sedes = $this->sedes->conColegio(['id_colegio' => $idColegio, 'eliminado' => 0]);
            $map = [];
            foreach ($sedes as $sede) {
                $nombre = strtolower((string) ($sede['nombre'] ?? ''));
                if (strpos($nombre, 'bogota') !== false || strpos($nombre, 'bogotá') !== false) {
                    $map['bogota'] = (int) $sede['id_sede'];
                }
                if (strpos($nombre, 'cota') !== false) {
                    $map['cota'] = (int) $sede['id_sede'];
                }
                if (!isset($map['default'])) {
                    $map['default'] = (int) $sede['id_sede'];
                }
            }
            $this->sedesCache[$idColegio] = $map;
        }

        $map = $this->sedesCache[$idColegio];
        $idSede = (int) ($map[$clave] ?? ($map['default'] ?? 0));
        if ($idSede <= 0) {
            throw new \RuntimeException('No se encontró una sede válida para el código del estudiante.');
        }

        return $idSede;
    }

    private function registrarDeuda(int $idColegio, int $idSede, int $idEstudiante, string $concepto, int $anio, int $mes, float $valor): void
    {
        $conceptoId = $this->conceptoId($idColegio, $concepto);
        $fechaGeneracion = sprintf('%04d-%02d-01', $anio, $mes);

        $existente = $this->deudas->all([
            'id_colegio' => $idColegio,
            'id_sede' => $idSede,
            'id_estudiante' => $idEstudiante,
            'id_concepto' => $conceptoId,
            'fecha_generacion' => $fechaGeneracion,
            'eliminado' => 0,
        ]);

        $payload = [
            'id_colegio' => $idColegio,
            'id_sede' => $idSede,
            'id_estudiante' => $idEstudiante,
            'id_concepto' => $conceptoId,
            'fecha_generacion' => $fechaGeneracion,
            'valor_inicial' => $valor,
            'saldo_actual' => $valor,
            'estado' => 'pendiente',
            'notas' => 'Origen: PHIDIAS',
            'eliminado' => 0,
        ];

        if ($existente) {
            $id = (int) $existente[0]['id_deuda'];
            $this->deudas->update($id, $payload);
            return;
        }

        $this->deudas->create($payload);
    }

    private function conceptoId(int $idColegio, string $nombre): int
    {
        $existente = $this->conceptos->all([
            'id_colegio' => $idColegio,
            'nombre' => $nombre,
            'eliminado' => 0,
        ]);

        if ($existente) {
            return (int) $existente[0]['id_concepto'];
        }

        return $this->conceptos->create([
            'id_colegio' => $idColegio,
            'nombre' => $nombre,
            'tipo' => 'phidias',
            'estado' => 'activo',
            'eliminado' => 0,
        ]);
    }

    private function buscarIndiceHoja(SimpleXLSX $xlsx, string $nombreHoja): int
    {
        $sheetNames = $xlsx->sheetNames();
        if (is_array($sheetNames)) {
            $indice = array_search($nombreHoja, $sheetNames, true);
            if ($indice !== false) {
                return (int) $indice;
            }
        }

        return 0; // Por defecto, la primera hoja
    }

    private function normalizarNumero($valor): float
    {
        if ($valor === null || $valor === '') {
            return 0.0;
        }

        if (is_numeric($valor)) {
            return (float) $valor;
        }

        $numero = preg_replace('/[^0-9\-.,]/', '', (string) $valor);
        $numero = str_replace(',', '', $numero);

        return (float) $numero;
    }

}
