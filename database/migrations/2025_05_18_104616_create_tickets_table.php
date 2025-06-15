<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id(); // ID único del ticket
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade'); // Cliente asociado al ticket
            $table->foreignId('agente_creador_id')->nullable()->constrained('users')->onDelete('set null'); // Agente que creó el ticket
            $table->foreignId('agente_asignado_id')->nullable()->constrained('users')->onDelete('set null'); // Agente actualmente asignado (puede ser el creador u otro)

            $table->string('asunto');
            $table->text('descripcion');
            $table->string('estado')->default('abierto'); // Ej: 'abierto', 'en_proceso', 'pendiente_cliente', 'resuelto', 'cerrado'
            $table->string('prioridad')->default('media'); // Ej: 'baja', 'media', 'alta', 'urgente'
            
            $table->timestamp('fecha_resolucion')->nullable(); // Cuándo se marcó como resuelto/cerrado
            $table->text('solucion_aplicada')->nullable(); // Descripción de la solución (si aplica)

            $table->timestamps(); // fecha_creacion (created_at) y fecha_ultima_actualizacion (updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
