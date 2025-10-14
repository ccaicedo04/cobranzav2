<?php

namespace App\Models;

use Core\Session;

class PagoModel extends BaseModel
{
    protected string $table = 'registro_pago';
    protected string $primaryKey = 'id_pago';
    protected array $fillable = [
        'id_colegio',
        'id_sede',
        'id_estudiante',
        'fecha_pago',
        'valor_total',
        'metodo_pago',
        'referencia',
        'observaciones',
        'ruta_soporte',
        'eliminado',
    ];

    public function porResponsable(int $idResponsable): array
    {
        $sql = 'SELECT p.*, e.nombre_completo AS estudiante_nombre
                FROM registro_pago p
                INNER JOIN estudiante e ON e.id_estudiante = p.id_estudiante
                WHERE p.eliminado = 0 AND e.id_responsable = :id_responsable';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_responsable' => $idResponsable]);

        return $stmt->fetchAll();
    }

    public function listadoCompleto(): array
    {
        $sql = 'SELECT p.*, e.nombre_completo AS estudiante_nombre
                FROM registro_pago p
                INNER JOIN estudiante e ON e.id_estudiante = p.id_estudiante
                WHERE p.eliminado = 0';
        $params = [];
        $user = Session::get('user');
        if ($user) {
            if (!empty($user['id_colegio'])) {
                $sql .= ' AND p.id_colegio = :id_colegio';
                $params[':id_colegio'] = $user['id_colegio'];
            }
            if (!empty($user['id_sede'])) {
                $sql .= ' AND p.id_sede = :id_sede';
                $params[':id_sede'] = $user['id_sede'];
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
