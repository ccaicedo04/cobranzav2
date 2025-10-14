<?php

namespace App\Models;

use PDO;

class UsuarioModel extends BaseModel
{
    protected string $table = 'usuario';
    protected string $primaryKey = 'id_usuario';
    protected array $fillable = [
        'id_colegio',
        'id_sede',
        'nombre_completo',
        'email',
        'usuario',
        'password_hash',
        'rol',
        'estado',
    ];

    public function authenticate(string $username, string $password): ?array
    {
        $sql = 'SELECT * FROM usuario WHERE usuario = :usuario AND estado = "activo" LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }

        return null;
    }

    public function updatePassword(int $idUsuario, string $password): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'UPDATE usuario SET password_hash = :hash WHERE id_usuario = :id';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['hash' => $hash, 'id' => $idUsuario]);
    }
}
