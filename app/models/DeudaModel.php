<?php

namespace App\Models;

use Core\Session;

class DeudaModel extends BaseModel
{
    /** @var string */
    protected $table = 'deuda';
    /** @var string */
    protected $primaryKey = 'id_deuda';
    /** @var array */
    protected $fillable = [
        'id_colegio',
        'id_sede',
        'id_estudiante',
        'id_periodo',
        'id_concepto',
        'fecha_generacion',
        'valor_inicial',
        'saldo_actual',
        'estado',
        'fecha_vencimiento',
        'notas',
        'eliminado',
    ];

    public function porResponsable(int $idResponsable): array
    {
        $sql = 'SELECT d.*, e.nombre_completo AS estudiante_nombre, c.nombre AS concepto_nombre
                FROM deuda d
                INNER JOIN estudiante e ON e.id_estudiante = d.id_estudiante
                LEFT JOIN concepto_deuda c ON c.id_concepto = d.id_concepto
                WHERE d.eliminado = 0 AND e.id_responsable = :id_responsable';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_responsable' => $idResponsable]);

        return $stmt->fetchAll();
    }

    public function listadoCompleto(): array
    {
        $sql = 'SELECT d.*, e.nombre_completo AS estudiante_nombre, c.nombre AS concepto_nombre, p.nombre AS periodo_nombre
                FROM deuda d
                INNER JOIN estudiante e ON e.id_estudiante = d.id_estudiante
                LEFT JOIN concepto_deuda c ON c.id_concepto = d.id_concepto
                LEFT JOIN periodo p ON p.id_periodo = d.id_periodo
                WHERE d.eliminado = 0';
        $params = [];
        $user = Session::get('user');
        if ($user) {
            if (!empty($user['id_colegio'])) {
                $sql .= ' AND d.id_colegio = :id_colegio';
                $params[':id_colegio'] = $user['id_colegio'];
            }
            if (!empty($user['id_sede'])) {
                $sql .= ' AND d.id_sede = :id_sede';
                $params[':id_sede'] = $user['id_sede'];
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function porEstudiante(int $idEstudiante): array
    {
        $sql = 'SELECT d.*, c.nombre AS concepto_nombre, p.nombre AS periodo_nombre'
            . ' FROM deuda d'
            . ' LEFT JOIN concepto_deuda c ON c.id_concepto = d.id_concepto'
            . ' LEFT JOIN periodo p ON p.id_periodo = d.id_periodo'
            . ' WHERE d.eliminado = 0 AND d.id_estudiante = :id_estudiante';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_estudiante' => $idEstudiante]);

        return $stmt->fetchAll();
    }
}
