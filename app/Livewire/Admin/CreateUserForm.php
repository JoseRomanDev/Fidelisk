<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CreateUserForm extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $rolesToAssign = []; // Array para los IDs de los roles a asignar

    public $allRoles = [];

    public function mount()
    {
        $this->allRoles = Role::all();
    }

    public function saveUser()
    {
        $validatedData = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'rolesToAssign' => ['required', 'array', 'min:1'], // Debe seleccionar al menos un rol
            'rolesToAssign.*' => ['exists:roles,id'], // Cada ID de rol debe existir en la tabla roles
            'extension_sip' => ['nullable', 'string', 'unique:users,extension_sip'],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'extension_sip' => $this->extension_sip,
        ]);

        $user->roles()->sync($this->rolesToAssign);

        session()->flash('message', "Usuario '{$user->name}' creado con éxito.");

        // Dispara un evento para que el componente UserManagement se refresque
        $this->dispatch('userCreated');

        // Limpia el formulario
        $this->reset();
        // Vuelve a cargar los roles después de resetear
        $this->mount();
    }

    public function render()
    {
        return view('livewire.admin.create-user-form');
    }
}