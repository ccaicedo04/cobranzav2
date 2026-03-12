<?php

namespace App\Models;

use Core\Session;

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
        $usuario = Session::get('user');
        $contexto = Session::get('context');
        $where = [];
        $params = [];

        if ($usuario) {
            $colegios = [];
            if (!empty($contexto['id_colegio'])) {
                $colegios[] = (int) $contexto['id_colegio'];
            } elseif (!empty($usuario['colegios_permitidos'])) {
                $colegios = array_map('intval', (array) $usuario['colegios_permitidos']);
            } elseif (!empty($usuario['id_colegio'])) {
                $colegios[] = (int) $usuario['id_colegio'];
            }

            if ($colegios) {
                $placeholders = [];
                foreach ($colegios as $index => $colegio) {
                    $placeholder = ':colegio_' . $index;
                    $placeholders[] = $placeholder;
                    $params[$placeholder] = $colegio;
                }
                $where[] = 'a.id_colegio IN (' . implode(',', $placeholders) . ')';
            }

            $sedes = [];
            if (!empty($contexto['id_sede'])) {
                $sedes[] = (int) $contexto['id_sede'];
            } elseif (!empty($usuario['sedes_permitidas'])) {
                $sedes = array_map('intval', (array) $usuario['sedes_permitidas']);
            } elseif (!empty($usuario['id_sede'])) {
                $sedes[] = (int) $usuario['id_sede'];
            }

            if ($sedes) {
                $placeholders = [];
                foreach ($sedes as $index => $sede) {
                    $placeholder = ':sede_' . $index;
                    $placeholders[] = $placeholder;
                    $params[$placeholder] = $sede;
                }
                $where[] = 'a.id_sede IN (' . implode(',', $placeholders) . ')';
            }
        }

        $sql = 'SELECT a.*, COALESCE(u.nombre_completo, CONCAT("ID ", a.id_usuario)) AS usuario_nombre,'
            . ' c.nombre AS colegio_nombre, s.nombre AS sede_nombre'
            . ' FROM auditoria_usuario a'
            . ' LEFT JOIN usuario u ON u.id_usuario = a.id_usuario'
            . ' LEFT JOIN colegio c ON c.id_colegio = a.id_colegio'
            . ' LEFT JOIN sede s ON s.id_sede = a.id_sede';

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY a.fecha_registro DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
