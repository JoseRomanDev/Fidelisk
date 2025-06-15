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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidos')->nullable();
            $table->string('telefono_principal')->unique();
            $table->string('email')->nullable()->unique();
            $table->text('direccion_completa')->nullable(); 
            $table->text('notas_agente')->nullable();      // Tus notas del agente
            $table->string('estado')->default('activo');   // 'activo', 'dado_de_baja'
            $table->date('fecha_baja')->nullable();        // Fecha específica para la baja

            // Campos para el cliente como contacto de empresa
            $table->boolean('es_contacto_empresa')->default(false); // Indica si este cliente es un contacto de una empresa
            $table->string('nombre_empresa_representada')->nullable(); // Nombre de la empresa a la que representa
            $table->string('puesto_contacto_empresa')->nullable();    // Puesto o rol del contacto en esa empresa
            $table->timestamps(); // Esto crea 'created_at' (fecha de alta) y 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
