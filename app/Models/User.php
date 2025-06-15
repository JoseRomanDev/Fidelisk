<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens; // Asegúrate de tener Sanctum instalado si lo usas
use App\Models\Role;
use App\Models\Ticket; // Necesitas importar Ticket para las relaciones de tickets
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Para la relación roles
use Illuminate\Database\Eloquent\Relations\HasMany;      // Para las relaciones de tickets

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        /**
         * Escucha el evento 'created'.
         * Este evento se dispara inmediatamente después de que un nuevo registro
         * de usuario ha sido insertado en la base de datos.
         */
        static::created(function (User $user) {
            // 1. Busca el rol 'agente' en la base de datos.
            //    Es más robusto buscar por un nombre fijo que por un ID que podría cambiar.
            $agenteRole = Role::where('name', 'agente')->first();

            // 2. Verifica si el rol 'agente' fue encontrado.
            if ($agenteRole) {
                // 3. Si se encontró, adjunta este rol al usuario recién creado.
                //    Esto crea una entrada en la tabla pivote 'role_user'
                //    vinculando este $user->id con el $agenteRole->id.
                $user->roles()->attach($agenteRole->id);
            }
            // else {
            //     \Log::error("Rol 'agente' por defecto no encontrado al crear usuario: " . $user->email);
            // }
        });
    } // Fin del método booted()
    protected $casts = [
    'email_verified_at' => 'datetime',
    'last_seen_at' => 'datetime', // <-- Añade esta línea
];
    // --- MÉTODOS DE RELACIÓN Y OTROS MÉTODOS DEBEN IR FUERA DE booted() ---

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Verifica si el usuario tiene un rol específico.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Obtiene los tickets creados por este usuario (agente).
     */
    public function ticketsCreados(): HasMany
    {
        return $this->hasMany(Ticket::class, 'agente_creador_id');
    }

    /**
     * Obtiene los tickets asignados a este usuario (agente).
     */
    public function ticketsAsignados(): HasMany
    {
        return $this->hasMany(Ticket::class, 'agente_asignado_id');
    }
    
    public function llamadasComoAgente(): HasMany
    {
        return $this->hasMany(Llamada::class, 'agente_id');
    }
} 