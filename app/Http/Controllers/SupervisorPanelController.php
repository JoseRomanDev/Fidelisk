<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupervisorPanelController extends Controller
{
    public function index()
    {
        
        return view('livewire.supervisor.panel');
    }
}
