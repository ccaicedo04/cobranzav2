<?php

namespace App\Models;

class AuditoriaModel extends BaseModel
{
    protected string $table = 'auditoria_usuario';
    protected string $primaryKey = 'id_auditoria';
    protected bool $softDelete = false;
    protected array $fillable = [
        'id_usuario',
        'id_colegio',
        'id_sede',
        'modulo',
        'accion',
        'detalle',
        'ip',
        'fecha_registro',
    ];

    public function conUsuarios(): array
    {
        $sql = 'SELECT a.*, u.nombre_completo AS usuario_nombre, c.nombre AS colegio_nombre, s.nombre AS sede_nombre'
            . ' FROM auditoria_usuario a'
            . ' LEFT JOIN usuario u ON u.id_usuario = a.id_usuario'
            . ' LEFT JOIN colegio c ON c.id_colegio = a.id_colegio'
            . ' LEFT JOIN sede s ON s.id_sede = a.id_sede'
            . ' ORDER BY a.fecha_registro DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
