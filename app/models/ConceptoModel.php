<?php

namespace App\Models;

class ConceptoModel extends BaseModel
{
    protected string $table = 'concepto_deuda';
    protected string $primaryKey = 'id_concepto';
    protected array $fillable = [
        'id_colegio',
        'nombre',
        'descripcion',
        'tipo',
        'valor_base',
        'estado',
        'eliminado',
    ];
}
