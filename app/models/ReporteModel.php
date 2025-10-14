<?php

namespace App\Models;

use Core\Database;
use Core\Session;
use PDO;

class ReporteModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    private function tenantConditions(): array
    {
        $user = Session::get('user');
        $where = [];
        $params = [];

        if ($user) {
            if (!empty($user['id_colegio'])) {
                $where[] = 'd.id_colegio = :id_colegio';
                $params[':id_colegio'] = $user['id_colegio'];
            }
            if (!empty($user['id_sede'])) {
                $where[] = 'd.id_sede = :id_sede';
                $params[':id_sede'] = $user['id_sede'];
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
        [$where, $params] = $this->tenantConditions();
        $sql = 'SELECT COALESCE(SUM(p.valor_total),0) FROM registro_pago p WHERE p.eliminado = 0 AND p.fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
        if ($where) {
            $sql .= ' AND ' . implode(' AND ', array_map(fn ($w) => str_replace('d.', 'p.', $w), $where));
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

        return $stmt->fetchAll();
    }

    public function recaudoUltimosMeses(int $meses = 6): array
    {
        [$where, $params] = $this->tenantConditions();
        $sql = 'SELECT DATE_FORMAT(p.fecha_pago, "%Y-%m") AS periodo, SUM(p.valor_total) AS total
                FROM registro_pago p
                WHERE p.eliminado = 0 AND p.fecha_pago >= DATE_SUB(CURDATE(), INTERVAL :meses MONTH)';
        if ($where) {
            $sql .= ' AND ' . implode(' AND ', array_map(fn ($w) => str_replace('d.', 'p.', $w), $where));
        }
        $sql .= ' GROUP BY periodo ORDER BY periodo';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':meses', $meses, PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
