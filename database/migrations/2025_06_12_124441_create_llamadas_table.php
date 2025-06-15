<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('llamadas', function (Blueprint $table) {
            
            $table->string('unique_id_asterisk')->primary(); // Esta es la clave principal

            $table->string('caller_id_num')->nullable();
            $table->string('caller_id_name')->nullable();
            $table->string('numero_destino')->nullable();
            $table->string('estado')->default('desconocido'); // Por ejemplo, 'sonando', 'en_curso', 'en_cola' 'finalizada'
            $table->timestamp('hora_inicio')->nullable();
            $table->timestamp('hora_atencion')->nullable();
            $table->timestamp('hora_fin')->nullable();
            $table->string('causa_fin')->nullable();

            // Clave foránea para relacionar con el usuario (agente)
            $table->unsignedBigInteger('agente_id')->nullable();
            $table->foreign('agente_id')->references('id')->on('users')->onDelete('set null');

            // Clave foránea para relacionar con el cliente (si es necesario)
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('set null');

            $table->string('extension_sip')->nullable(); // Para almacenar la extensión SIP del agente que atendió

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('llamadas');
    }
};