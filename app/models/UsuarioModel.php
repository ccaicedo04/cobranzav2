<?php

namespace App\Models;

use PDO;

class UsuarioModel extends BaseModel
{
    /** @var string */
    protected $table = 'usuario';
    /** @var string */
    protected $primaryKey = 'id_usuario';
    /** @var array */
    protected $fillable = [
        'id_colegio',
        'id_sede',
        'nombre_completo',
        'email',
        'usuario',
        'password_hash',
        'rol',
        'estado',
    ];

    /** @var array */
    protected $tenantColumns = [];

    public function listadoConContexto(array $restricciones = []): array
    {
        $where = ['u.eliminado = 0'];
        $params = [];

        $joins = 'LEFT JOIN colegio c ON c.id_colegio = u.id_colegio
                LEFT JOIN sede s ON s.id_sede = u.id_sede
                LEFT JOIN usuario_colegio uc ON uc.id_usuario = u.id_usuario';

        if (!empty($restricciones['colegios'])) {
            $colegios = array_values(array_unique(array_filter(array_map('intval', (array) $restricciones['colegios']))));
            if ($colegios) {
                $placeholders = [];
                foreach ($colegios as $index => $colegio) {
                    $placeholder = ':colegio_' . $index;
                    $placeholders[] = $placeholder;
                    $params[$placeholder] = $colegio;
                }
                $inClause = implode(',', $placeholders);
                $where[] = '(
                    (u.id_colegio IS NOT NULL AND u.id_colegio IN (' . $inClause . '))
                    OR uc.id_colegio IN (' . $inClause . ')
                )';
            }
        }

        if (!empty($restricciones['roles_permitidos'])) {
            $roles = array_values(array_unique(array_filter((array) $restricciones['roles_permitidos'], static fn ($rol) => is_string($rol) && $rol !== '')));
            if ($roles) {
                $placeholders = [];
                foreach ($roles as $index => $rol) {
                    $placeholder = ':rol_' . $index;
                    $placeholders[] = $placeholder;
                    $params[$placeholder] = $rol;
                }
                $where[] = 'u.rol IN (' . implode(',', $placeholders) . ')';
            }
        }

        if (!empty($restricciones['roles_excluidos'])) {
            $roles = array_values(array_unique(array_filter((array) $restricciones['roles_excluidos'], static fn ($rol) => is_string($rol) && $rol !== '')));
            if ($roles) {
                $placeholders = [];
                foreach ($roles as $index => $rol) {
                    $placeholder = ':rol_ex_' . $index;
                    $placeholders[] = $placeholder;
                    $params[$placeholder] = $rol;
                }
                $where[] = 'u.rol NOT IN (' . implode(',', $placeholders) . ')';
            }
        }

        $sql = 'SELECT u.*, c.nombre AS colegio_nombre, s.nombre AS sede_nombre
                FROM usuario u
                ' . $joins;

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' GROUP BY u.id_usuario
                 ORDER BY u.nombre_completo';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ids = array_map(static fn ($fila) => (int) $fila['id_usuario'], $usuarios);
        $asignaciones = $this->asignacionesMasivas($ids);

        foreach ($usuarios as &$usuario) {
            $id = (int) $usuario['id_usuario'];
            $usuario['colegios_asignados'] = $asignaciones['colegios'][$id] ?? [];
            $usuario['sedes_asignadas'] = $asignaciones['sedes'][$id] ?? [];
            $usuario['modulos_asignados'] = $asignaciones['modulos'][$id] ?? [];
            $usuario['permisos_colegios_array'] = array_map(
                static fn (array $colegio) => (int) $colegio['id_colegio'],
                $usuario['colegios_asignados']
            );
            $usuario['permisos_sedes_array'] = array_map(
                static fn (array $sede) => (int) $sede['id_sede'],
                $usuario['sedes_asignadas']
            );
            $usuario['permisos_modulos_array'] = array_map(
                static fn (array $modulo) => $modulo['codigo'],
                $usuario['modulos_asignados']
            );
        }
        unset($usuario);

        return $usuarios;
    }

    public function authenticate(string $username, string $password): ?array
    {
        $sql = 'SELECT u.*, c.nombre AS colegio_nombre, s.nombre AS sede_nombre
                FROM usuario u
                LEFT JOIN colegio c ON c.id_colegio = u.id_colegio
                LEFT JOIN sede s ON s.id_sede = u.id_sede
                WHERE u.usuario = :usuario AND u.estado = "activo" LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $user['asignaciones'] = $this->asignacionesUsuario((int) $user['id_usuario']);
            return $user;
        }

        return null;
    }

    public function detalle(int $idUsuario): ?array
    {
        $sql = 'SELECT u.*, c.nombre AS colegio_nombre, s.nombre AS sede_nombre
                FROM usuario u
                LEFT JOIN colegio c ON c.id_colegio = u.id_colegio
                LEFT JOIN sede s ON s.id_sede = u.id_sede
                WHERE u.id_usuario = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idUsuario]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        $row['asignaciones'] = $this->asignacionesUsuario($idUsuario);

        return $row;
    }

    public function asignacionesUsuario(int $idUsuario): array
    {
        $asignadas = $this->asignacionesMasivas([$idUsuario]);

        return [
            'colegios' => $asignadas['colegios'][$idUsuario] ?? [],
            'sedes' => $asignadas['sedes'][$idUsuario] ?? [],
            'modulos' => $asignadas['modulos'][$idUsuario] ?? [],
        ];
    }

    public function updatePassword(int $idUsuario, string $password): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'UPDATE usuario SET password_hash = :hash WHERE id_usuario = :id';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['hash' => $hash, 'id' => $idUsuario]);
    }

    public function syncColegios(int $idUsuario, array $colegios): void
    {
        $this->syncPivot('usuario_colegio', 'id_colegio', $idUsuario, array_map('intval', $colegios));
    }

    public function syncSedes(int $idUsuario, array $sedes): void
    {
        $this->syncPivot('usuario_sede', 'id_sede', $idUsuario, array_map('intval', $sedes));
    }

    public function syncModulosPorCodigo(int $idUsuario, array $codigos): void
    {
        $codigos = array_values(array_unique(array_filter($codigos, static fn ($codigo) => $codigo !== null && $codigo !== '')));
        if (!$codigos) {
            $this->syncPivot('usuario_modulo', 'id_modulo', $idUsuario, []);
            return;
        }

        $placeholders = implode(',', array_fill(0, count($codigos), '?'));
        $sql = 'SELECT id_modulo FROM modulo_sistema WHERE codigo IN (' . $placeholders . ') AND estado = "activo"';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($codigos);
        $ids = array_map(static fn ($row) => (int) $row['id_modulo'], $stmt->fetchAll(PDO::FETCH_ASSOC));

        $this->syncPivot('usuario_modulo', 'id_modulo', $idUsuario, $ids);
    }

    private function syncPivot(string $tabla, string $columna, int $idUsuario, array $valores): void
    {
        $this->db->beginTransaction();
        $delete = $this->db->prepare('DELETE FROM ' . $tabla . ' WHERE id_usuario = :id_usuario');
        $delete->execute([':id_usuario' => $idUsuario]);

        if ($valores) {
            $placeholders = [];
            $params = [];
            foreach ($valores as $valor) {
                $placeholders[] = '(?, ?)';
                $params[] = $idUsuario;
                $params[] = $valor;
            }
            $sql = 'INSERT INTO ' . $tabla . ' (id_usuario, ' . $columna . ') VALUES ' . implode(',', $placeholders);
            $insert = $this->db->prepare($sql);
            $insert->execute($params);
        }

        $this->db->commit();
    }

    private function asignacionesMasivas(array $ids): array
    {
        $resultado = [
            'colegios' => [],
            'sedes' => [],
            'modulos' => [],
        ];

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (!$ids) {
            return $resultado;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sqlColegios = 'SELECT uc.id_usuario, c.id_colegio, c.nombre, c.nit
                        FROM usuario_colegio uc
                        INNER JOIN colegio c ON c.id_colegio = uc.id_colegio
                        WHERE uc.id_usuario IN (' . $placeholders . ')';
        $stmt = $this->db->prepare($sqlColegios);
        $stmt->execute($ids);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $idUsuario = (int) $fila['id_usuario'];
            $resultado['colegios'][$idUsuario][] = [
                'id_colegio' => (int) $fila['id_colegio'],
                'nombre' => $fila['nombre'],
                'nit' => $fila['nit'],
            ];
        }

        $sqlSedes = 'SELECT us.id_usuario, s.id_sede, s.nombre, s.id_colegio, c.nombre AS colegio_nombre
                     FROM usuario_sede us
                     INNER JOIN sede s ON s.id_sede = us.id_sede
                     INNER JOIN colegio c ON c.id_colegio = s.id_colegio
                     WHERE us.id_usuario IN (' . $placeholders . ')';
        $stmt = $this->db->prepare($sqlSedes);
        $stmt->execute($ids);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $idUsuario = (int) $fila['id_usuario'];
            $resultado['sedes'][$idUsuario][] = [
                'id_sede' => (int) $fila['id_sede'],
                'id_colegio' => (int) $fila['id_colegio'],
                'nombre' => $fila['nombre'],
                'colegio_nombre' => $fila['colegio_nombre'],
            ];
        }

        $sqlModulos = 'SELECT um.id_usuario, m.id_modulo, m.codigo, m.nombre
                        FROM usuario_modulo um
                        INNER JOIN modulo_sistema m ON m.id_modulo = um.id_modulo
                        WHERE um.id_usuario IN (' . $placeholders . ') AND m.estado = "activo"';
        $stmt = $this->db->prepare($sqlModulos);
        $stmt->execute($ids);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $idUsuario = (int) $fila['id_usuario'];
            $resultado['modulos'][$idUsuario][] = [
                'id_modulo' => (int) $fila['id_modulo'],
                'codigo' => $fila['codigo'],
                'nombre' => $fila['nombre'],
            ];
        }

        return $resultado;
    }
}
