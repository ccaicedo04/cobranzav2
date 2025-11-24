<?php

namespace App\Models;

use Core\Database;
use Core\Session;
use DateInterval;
use DateTimeImmutable;
use PDO;

class ReporteModel
{
    /** @var PDO */
    private $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    private function tenantConditions(array $columnMap = []): array
    {
        $user = Session::get('user');
        $context = Session::get('context');
        $where = [];
        $params = [];

        $columnMap = array_merge([
            'colegio' => 'd.id_colegio',
            'sede' => 'd.id_sede',
        ], $columnMap);

        if ($user) {
            $colegios = [];
            if (!empty($context['id_colegio'])) {
                $colegios[] = (int) $context['id_colegio'];
            } elseif (!empty($user['colegios_permitidos'])) {
                $colegios = array_map('intval', (array) $user['colegios_permitidos']);
            } elseif (!empty($user['id_colegio'])) {
                $colegios[] = (int) $user['id_colegio'];
            }

            if ($colegios) {
                $placeholders = [];
                foreach ($colegios as $idx => $colegio) {
                    $placeholder = ':colegio_' . $idx;
                    $placeholders[] = $placeholder;
                    $params[$placeholder] = $colegio;
                }
                $where[] = $columnMap['colegio'] . ' IN (' . implode(',', $placeholders) . ')';
            }

            $sedes = [];
            if (!empty($context['id_sede'])) {
                $sedes[] = (int) $context['id_sede'];
            } elseif (!empty($user['sedes_permitidas'])) {
                $sedes = array_map('intval', (array) $user['sedes_permitidas']);
            } elseif (!empty($user['id_sede'])) {
                $sedes[] = (int) $user['id_sede'];
            }

            if ($sedes) {
                $placeholders = [];
                foreach ($sedes as $idx => $sede) {
                    $placeholder = ':sede_' . $idx;
                    $placeholders[] = $placeholder;
                    $params[$placeholder] = $sede;
                }
                $where[] = $columnMap['sede'] . ' IN (' . implode(',', $placeholders) . ')';
            }
        }

        return [$where, $params];
    }

    public function carteraPendiente(): float
    {
        [$where, $params] = $this->tenantConditions();
        $sql = 'SELECT COALESCE(SUM(d.saldo_actual),0) AS total FROM deuda d WHERE d.eliminado = 0';
        if ($where) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (float) $stmt->fetchColumn();
    }

    public function totalPagosUltimoMes(): float
    {
        [$where, $params] = $this->tenantConditions([
            'colegio' => 'p.id_colegio',
            'sede' => 'p.id_sede',
        ]);
        $sql = 'SELECT COALESCE(SUM(p.valor_total),0) FROM registro_pago p WHERE p.eliminado = 0 AND p.fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
        if ($where) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (float) $stmt->fetchColumn();
    }

    public function topResponsables(int $limit = 5): array
    {
        [$where, $params] = $this->tenantConditions();
        $sql = 'SELECT r.nombre_completo, SUM(d.saldo_actual) AS total
                FROM deuda d
                INNER JOIN estudiante e ON e.id_estudiante = d.id_estudiante
                INNER JOIN responsable_financiero r ON r.id_responsable = e.id_responsable
                WHERE d.eliminado = 0 AND e.eliminado = 0 AND r.eliminado = 0';
        if ($where) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }
        $sql .= ' GROUP BY r.id_responsable ORDER BY total DESC LIMIT :limit';
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function carteraUltimosMeses(int $meses = 6): array
    {
        [$where, $params] = $this->tenantConditions();
        $sql = 'SELECT DATE_FORMAT(d.fecha_generacion, "%Y-%m") AS periodo, SUM(d.saldo_actual) AS total
                FROM deuda d
                WHERE d.eliminado = 0 AND d.fecha_generacion >= DATE_SUB(CURDATE(), INTERVAL :meses MONTH)';
        if ($where) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }
        $sql .= ' GROUP BY periodo ORDER BY periodo';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':meses', $meses, PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        $rows = $stmt->fetchAll();

        return $this->fillMonths($rows, $meses);
    }

    public function recaudoUltimosMeses(int $meses = 6): array
    {
        [$where, $params] = $this->tenantConditions([
            'colegio' => 'p.id_colegio',
            'sede' => 'p.id_sede',
        ]);
        $sql = 'SELECT DATE_FORMAT(p.fecha_pago, "%Y-%m") AS periodo, SUM(p.valor_total) AS total
                FROM registro_pago p
                WHERE p.eliminado = 0 AND p.fecha_pago >= DATE_SUB(CURDATE(), INTERVAL :meses MONTH)';
        if ($where) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }
        $sql .= ' GROUP BY periodo ORDER BY periodo';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':meses', $meses, PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        $rows = $stmt->fetchAll();

        return $this->fillMonths($rows, $meses);
    }

    public function reporteCartera(array $filtros = []): array
    {
        [$where, $params] = $this->tenantConditions();
        $sql = 'SELECT d.id_deuda, d.fecha_generacion, d.fecha_vencimiento, d.valor_inicial, d.saldo_actual, d.estado,
                       e.nombre_completo AS estudiante, r.nombre_completo AS responsable,
                       cd.nombre AS concepto, p.nombre AS periodo,
                       c.nombre AS colegio_nombre, s.nombre AS sede_nombre
                FROM deuda d
                INNER JOIN estudiante e ON e.id_estudiante = d.id_estudiante
                INNER JOIN responsable_financiero r ON r.id_responsable = e.id_responsable
                LEFT JOIN concepto_deuda cd ON cd.id_concepto = d.id_concepto
                LEFT JOIN periodo p ON p.id_periodo = d.id_periodo
                LEFT JOIN colegio c ON c.id_colegio = d.id_colegio
                LEFT JOIN sede s ON s.id_sede = d.id_sede
                WHERE d.eliminado = 0';

        if ($where) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }

        if (!empty($filtros['estado'])) {
            $sql .= ' AND d.estado = :estado';
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['desde'])) {
            $sql .= ' AND d.fecha_generacion >= :fecha_desde';
            $params[':fecha_desde'] = $filtros['desde'];
        }

        if (!empty($filtros['hasta'])) {
            $sql .= ' AND d.fecha_generacion <= :fecha_hasta';
            $params[':fecha_hasta'] = $filtros['hasta'];
        }

        $sql .= ' ORDER BY d.fecha_generacion DESC, d.id_deuda DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function reportePagos(array $filtros = []): array
    {
        [$where, $params] = $this->tenantConditions([
            'colegio' => 'p.id_colegio',
            'sede' => 'p.id_sede',
        ]);

        $sql = 'SELECT p.id_pago, p.fecha_pago, p.valor_total, p.metodo_pago, p.referencia, p.observaciones,
                       e.nombre_completo AS estudiante, r.nombre_completo AS responsable,
                       c.nombre AS colegio_nombre, s.nombre AS sede_nombre
                FROM registro_pago p
                INNER JOIN estudiante e ON e.id_estudiante = p.id_estudiante
                INNER JOIN responsable_financiero r ON r.id_responsable = e.id_responsable
                LEFT JOIN colegio c ON c.id_colegio = p.id_colegio
                LEFT JOIN sede s ON s.id_sede = p.id_sede
                WHERE p.eliminado = 0';

        if ($where) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }

        if (!empty($filtros['metodo'])) {
            $sql .= ' AND p.metodo_pago = :metodo';
            $params[':metodo'] = $filtros['metodo'];
        }

        if (!empty($filtros['desde'])) {
            $sql .= ' AND p.fecha_pago >= :pagos_desde';
            $params[':pagos_desde'] = $filtros['desde'];
        }

        if (!empty($filtros['hasta'])) {
            $sql .= ' AND p.fecha_pago <= :pagos_hasta';
            $params[':pagos_hasta'] = $filtros['hasta'];
        }

        $sql .= ' ORDER BY p.fecha_pago DESC, p.id_pago DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function reporteAcuerdos(array $filtros = []): array
    {
        [$where, $params] = $this->tenantConditions([
            'colegio' => 'a.id_colegio',
            'sede' => 'a.id_sede',
        ]);

        $sql = 'SELECT a.id_acuerdo, a.fecha_inicio, a.fecha_fin, a.monto_total, a.cuotas, a.estado, a.observaciones,
                       r.nombre_completo AS responsable, e.nombre_completo AS estudiante,
                       c.nombre AS colegio_nombre, s.nombre AS sede_nombre
                FROM acuerdo_pago a
                INNER JOIN responsable_financiero r ON r.id_responsable = a.id_responsable
                LEFT JOIN estudiante e ON e.id_estudiante = a.id_estudiante
                LEFT JOIN colegio c ON c.id_colegio = a.id_colegio
                LEFT JOIN sede s ON s.id_sede = a.id_sede
                WHERE a.eliminado = 0';

        if ($where) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }

        if (!empty($filtros['estado'])) {
            $sql .= ' AND a.estado = :estado_acuerdo';
            $params[':estado_acuerdo'] = $filtros['estado'];
        }

        if (!empty($filtros['desde'])) {
            $sql .= ' AND a.fecha_inicio >= :acuerdo_desde';
            $params[':acuerdo_desde'] = $filtros['desde'];
        }

        if (!empty($filtros['hasta'])) {
            $sql .= ' AND a.fecha_inicio <= :acuerdo_hasta';
            $params[':acuerdo_hasta'] = $filtros['hasta'];
        }

        $sql .= ' ORDER BY a.fecha_inicio DESC, a.id_acuerdo DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function metodosPagoDisponibles(): array
    {
        [$where, $params] = $this->tenantConditions([
            'colegio' => 'p.id_colegio',
            'sede' => 'p.id_sede',
        ]);
        $sql = 'SELECT DISTINCT metodo_pago FROM registro_pago p WHERE p.eliminado = 0 AND p.metodo_pago IS NOT NULL AND p.metodo_pago <> ""';
        if ($where) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY metodo_pago';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    private function fillMonths(array $rows, int $meses): array
    {
        $map = [];
        foreach ($rows as $row) {
            if (!isset($row['periodo'])) {
                continue;
            }
            $map[$row['periodo']] = (float) ($row['total'] ?? 0);
        }

        $resultado = [];
        $inicio = new DateTimeImmutable('first day of this month');
        for ($i = $meses - 1; $i >= 0; $i--) {
            $periodo = $inicio->sub(new DateInterval('P' . $i . 'M'))->format('Y-m');
            $resultado[] = [
                'periodo' => $periodo,
                'total' => $map[$periodo] ?? 0,
            ];
        }

        return $resultado;
    }
}
