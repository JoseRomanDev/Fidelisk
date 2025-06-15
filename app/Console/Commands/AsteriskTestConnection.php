<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PAMI\Client\Impl\ClientImpl as PamiClient;
use PAMI\Message\Action\PingAction;

class AsteriskTestConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asterisk:test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the connection to the Asterisk Manager Interface (AMI)';

    
    public function handle()
    {
        $this->info('Intentando conectar con Asterisk (AMI)...');

        // Depuración: mostrar valores de las variables de entorno
        $this->info('Host: ' . env('ASTERISK_HOST'));
        $this->info('Port: ' . env('ASTERISK_PORT'));
        $this->info('Username: ' . env('ASTERISK_USERNAME'));
        
        $options = [
            'host' => env('ASTERISK_HOST'),
            'scheme' => 'tcp://',
            'port' => env('ASTERISK_PORT'),
            'username' => env('ASTERISK_USERNAME'),
            'secret' => env('ASTERISK_SECRET'),
            'connect_timeout' => 10,
            'read_timeout' => 10,
        ];

        try {
            $pamiClient = new PamiClient($options);

            // Abrimos la conexión
            $pamiClient->open();

            // Hacemos un ping para verificar que está vivo
            $response = $pamiClient->send(new PingAction());

            if ($response->isSuccess()) {
                $this->info('¡Conexión con Asterisk (AMI) exitosa!');
                $this->comment('Respuesta del Ping: ' . $response->getMessage());
            } else {
                $this->error('La conexión se estableció, pero el Ping falló.');
                $this->comment('Respuesta: ' . $response->getMessage());
            }
            
            // Cerramos la conexión
            $pamiClient->close();

        } catch (\Exception $e) {
            $this->error('Error al conectar con Asterisk (AMI):');
            $this->error($e->getMessage());
            $this->comment('Por favor, verifica las credenciales y la IP en tu archivo .env y que Asterisk esté funcionando.');
        }
    }
    
}