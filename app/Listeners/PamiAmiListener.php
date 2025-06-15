<?php
namespace App\Listeners;

use PAMI\Listener\IEventListener;
use PAMI\Message\Event\EventMessage;
use App\Console\Commands\AsteriskListen; // Para pasar la instancia del comando

class PamiAmiListener implements IEventListener
{
    protected AsteriskListen $commandInstance;

    public function __construct(AsteriskListen $commandInstance)
    {
        $this->commandInstance = $commandInstance;
    }

    
    public function handle(EventMessage $event): void
    {
        // Delega el procesamiento del evento a un método público en tu comando AsteriskListen
        $this->commandInstance->processPamiEvent($event);
    }
}