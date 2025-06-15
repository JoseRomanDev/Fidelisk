<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgentePanelController extends Controller
{
   
    public function index()
    {
        
        return view('livewire.agente.panel');
    }
}