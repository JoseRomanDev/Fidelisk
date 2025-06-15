<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar de forma masiva.
     * Laravel protege contra vulnerabilidades de asignación masiva por defecto.
     * Al definir aquí los campos, indicamos explícitamente cuáles son seguros
     * para ser rellenados usando métodos como Cliente::create($data) o $cliente->update($data).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'apellidos',
        'telefono_principal',
        'email',
        'direccion_completa',
        'notas_agente',
        'estado',
        'fecha_baja',
        'es_contacto_empresa',
        'nombre_empresa_representada',
        'puesto_contacto_empresa',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * Esta propiedad permite definir cómo se deben convertir los atributos del modelo
     * cuando se leen o se escriben en la base de datos. Por ejemplo, convertir
     * un entero (0 o 1) de la base de datos a un booleano (true/false) en PHP,
     * o una cadena de fecha a un objeto Carbon para facilitar su manipulación.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'es_contacto_empresa' => 'boolean',
        'fecha_baja' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    
     public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function llamadasClientes(): HasMany
    {
        return $this->hasMany(Llamada::class, 'cliente_id');
    }
}
