<?php

namespace App\Models;

class EstudianteModel extends BaseModel
{
    protected string $table = 'estudiante';
    protected string $primaryKey = 'id_estudiante';
    protected array $fillable = [
        'id_colegio',
        'id_sede',
        'id_responsable',
        'codigo_estudiante',
        'nombre_completo',
        'grado',
        'curso',
        'estado',
        'eliminado',
    ];
}
