<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
{
    $this->validate();
    $this->ensureIsNotRateLimited();

    // Intenta autenticar al usuario
    if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
        // Éxito en la autenticación
        RateLimiter::clear($this->throttleKey());
        Session::regenerate(); // Regenera la sesión después de la autenticación exitosa

        $user = Auth::user();
        $user->is_online = true;
        $user->last_seen_at = now();
        $user->save();

        // Redirección basada en el rol
        
    if ($user->hasRole('admin')) {
    $this->redirect(route('admin.panel'), navigate: true);
    } elseif ($user->hasRole('supervisor')) {
    $this->redirect(route('supervisor.panel'), navigate: true);
    } elseif ($user->hasRole('agente')) {
    $this->redirect(route('agente.panel'), navigate: true);
    } else {
    $this->redirect(route('dashboard'), navigate: true); 
    }

    } else {
        
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }
    }

    public function logout(): void
    {
    $user = Auth::user(); // Obtén el usuario ANTES de hacer logout
    if ($user) {
        $user->is_online = false;
        $user->last_seen_at = now(); 
        $user->save();
    }

    Auth::logout(); // Ahora cierra la sesión
    session()->invalidate();
    session()->regenerateToken();

    $this->redirect(route('login'), navigate: true);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
    
}
