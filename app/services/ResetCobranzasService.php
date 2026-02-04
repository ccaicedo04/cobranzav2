<?php

namespace App\Services;

use Core\Database;
use PDO;

class ResetCobranzasService
{
    private const TABLAS_OBJETIVO = [
        'comunicacion',
        'acuerdo_pago',
        'registro_pago',
        'deuda',
        'estudiante',
        'responsable_financiero',
    ];

    /** @var PDO */
    private $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function resumen(): array
    {
        $resumen = [];
        foreach (self::TABLAS_OBJETIVO as $tabla) {
            $stmt = $this->db->query('SELECT COUNT(*) AS total FROM ' . $tabla);
            $resumen[$tabla] = (int) ($stmt->fetchColumn() ?: 0);
        }

        return $resumen;
    }

    /**
     * Genera un respaldo en disco y devuelve la ruta absoluta.
     */
    public function crearRespaldo(): string
    {
        $baseDir = dirname(__DIR__, 2) . '/storage/backups';
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }

        $filename = sprintf('respaldo_cobranzas_%s.sql', date('Ymd_His'));
        $path = $baseDir . '/' . $filename;

        $contenido = $this->construirSqlDump();
        file_put_contents($path, $contenido);

        return $path;
    }

    /**
     * Elimina la información de cobranza sin afectar usuarios ni configuraciones.
     */
    public function limpiar(): void
    {
        $this->db->exec('SET FOREIGN_KEY_CHECKS=0');
        foreach (self::TABLAS_OBJETIVO as $tabla) {
            $this->db->exec('TRUNCATE TABLE ' . $tabla);
        }
        $this->db->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    public function respaldarYLimpiar(): string
    {
        $path = $this->crearRespaldo();
        $this->limpiar();

        return $path;
    }

    private function construirSqlDump(): string
    {
        $dump = [];
        $dump[] = '-- Respaldo de información de cobranzas';
        $dump[] = '-- Generado: ' . date('c');
        $dump[] = 'SET FOREIGN_KEY_CHECKS=0;';

        foreach (self::TABLAS_OBJETIVO as $tabla) {
            $dump[] = '';
            $dump[] = '-- Estructura para tabla ' . $tabla;
            $createStmt = $this->db->query('SHOW CREATE TABLE ' . $tabla);
            $create = $createStmt ? $createStmt->fetch(PDO::FETCH_ASSOC) : null;
            if (!empty($create['Create Table'])) {
                $dump[] = 'DROP TABLE IF EXISTS `' . $tabla . '`;';
                $dump[] = $create['Create Table'] . ';';
            }

            $dump[] = '-- Datos de ' . $tabla;
            $stmt = $this->db->query('SELECT * FROM ' . $tabla);
            $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            if (!$rows) {
                continue;
            }

            $columnas = array_map(static fn ($col) => '`' . $col . '`', array_keys($rows[0]));
            $chunks = array_chunk($rows, 200);
            foreach ($chunks as $grupo) {
                $values = [];
                foreach ($grupo as $fila) {
                    $filaQuoted = array_map(function ($valor) {
                        if ($valor === null) {
                            return 'NULL';
                        }
                        return $this->db->quote((string) $valor);
                    }, array_values($fila));
                    $values[] = '(' . implode(', ', $filaQuoted) . ')';
                }

                $dump[] = sprintf(
                    'INSERT INTO `%s` (%s) VALUES %s;',
                    $tabla,
                    implode(', ', $columnas),
                    implode(', ', $values)
                );
            }
        }

        $dump[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode("\n", $dump) . "\n";
    }
}
