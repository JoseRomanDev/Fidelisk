<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Llamada extends Model
{
    use HasFactory;
    protected $primaryKey = 'unique_id_asterisk';

    // Indica que la clave primaria NO es autoincrementable, ya que Asterisk genera el ID.
    public $incrementing = false;

    // Define el tipo de la clave primaria. Los UniqueId de Asterisk suelen ser strings.
    protected $keyType = 'string';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unique_id_asterisk',
        'caller_id_num',
        'caller_id_name',
        'numero_destino',
        'estado',
        'hora_inicio',
        'hora_atencion',
        'hora_fin',
        'causa_fin',
        'agente_id',
        'cliente_id',
        'extension_sip'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hora_inicio' => 'datetime',
        'hora_atencion' => 'datetime',
        'hora_fin' => 'datetime',
    ];

    public function agente()
{
    return $this->belongsTo(User::class, 'agente_id');
}

    public function cliente()
{
    return $this->belongsTo(Cliente::class, 'cliente_id');
}
}

