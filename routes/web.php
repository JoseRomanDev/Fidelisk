<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentePanelController;
use App\Http\Controllers\SupervisorPanelController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Middleware\CheckUserRole;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

    Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    
    // Supervisor routes
    Route::middleware(['auth', CheckUserRole::class . ':supervisor,admin'])->group(function () {
    Route::get('/supervisor/panel', [SupervisorPanelController::class, 'index'])->name('supervisor.panel');
});
    // Agente routes
    Route::middleware(['auth', CheckUserRole::class . ':agente,supervisor,admin'])->group(function () { 
    Route::get('/agente/panel', [AgentePanelController::class, 'index'])->name('agente.panel');
});

    // Admin routes
    Route::middleware(['auth', CheckUserRole::class . ':admin'])->group(function () { 
    Route::get('/admin/panel', [AdminPanelController::class, 'index'])->name('admin.panel');
});

});

require __DIR__.'/auth.php';
