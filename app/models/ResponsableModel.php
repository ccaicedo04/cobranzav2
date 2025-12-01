<?php

namespace App\Models;

class ResponsableModel extends BaseModel
{
    /** @var string */
    protected $table = 'responsable_financiero';
    /** @var string */
    protected $primaryKey = 'id_responsable';
    /** @var array */
    protected $fillable = [
        'id_colegio',
        'id_sede',
        'nombre_completo',
        'tipo_documento',
        'numero_documento',
        'telefono',
        'correo',
        'direccion',
        'estado',
        'eliminado',
    ];

    /**
     * @return array|null
     */
    public function buscarPorDocumento(string $documento, int $idColegio, int $idSede)
    {
        $documento = trim($documento);
        if ($documento === '') {
            return null;
        }

        $coincidencias = $this->all([
            'numero_documento' => $documento,
            'id_colegio' => $idColegio,
            'id_sede' => $idSede,
            'eliminado' => 0,
        ], ['order' => 'id_responsable DESC']);

        return $coincidencias[0] ?? null;
    }

    public function conContexto(array $filtros = []): array
    {
        $filtros = $this->applyTenantFilters($filtros);
        [$where, $params] = $this->compileFilters($filtros, 'r');
        $where[] = 'r.eliminado = 0';

        $sql = 'SELECT r.*, c.nombre AS colegio_nombre, s.nombre AS sede_nombre'
            . ' FROM responsable_financiero r'
            . ' INNER JOIN colegio c ON c.id_colegio = r.id_colegio'
            . ' INNER JOIN sede s ON s.id_sede = r.id_sede';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY c.nombre, s.nombre, r.nombre_completo';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * @return array|null
     */
    public function buscarPorTelefono(string $telefono)
    {
        $telefono = preg_replace('/\D+/', '', $telefono);
        if ($telefono === '') {
            return null;
        }

        $variantes = [$telefono];
        if (strlen($telefono) === 10 && $telefono[0] === '3') {
            $variantes[] = '57' . $telefono;
        }
        if (strlen($telefono) > 2 && str_starts_with($telefono, '57')) {
            $variantes[] = substr($telefono, 2);
        }

        $placeholders = implode(',', array_fill(0, count($variantes), '?'));
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE eliminado = 0 AND REPLACE(REPLACE(REPLACE(telefono, " ", ""), "-", ""), "+", "") IN (' . $placeholders . ') LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($variantes);

        $resultado = $stmt->fetch();

        return $resultado ?: null;
    }
}
