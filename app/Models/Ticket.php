<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'agente_creador_id',
        'agente_asignado_id',
        'asunto',
        'descripcion',
        'estado',
        'prioridad',
        'fecha_resolucion',
        'solucion_aplicada',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fecha_resolucion' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function agenteCreador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agente_creador_id');
    }

    public function agenteAsignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agente_asignado_id');
    }
}