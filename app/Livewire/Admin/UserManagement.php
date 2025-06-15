<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Role; 
use Livewire\WithPagination;
use Livewire\Attributes\On;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';

    // Propiedades para el modal de edición de roles
    public ?User $editingUser = null;
    public $editingUserData = [  
        'name' => '',
        'email' => '',
        'extension_sip' => '',
    ];
    public $showEditModal = false;
    public $userRoles = []; // Array para los IDs de los roles seleccionados
    public $allRoles = []; // Para almacenar todos los roles posibles
    public $editingUserPassword = ''; // Para almacenar la nueva contraseña del usuario que se está editando
    public $editingUserPassword_confirmation = ''; //Confirmar nueva contraseña
    // Propiedades para el modal de creación de usuarios
    public $showCreateModal = false;
      #[On('userCreated')]
    public function onUserCreated()
    {
        $this->closeCreateModal(); // Cierra el modal de creación
        // El refresco de la lista ya ocurre automáticamente con el re-render
    }
    
    // Método para abrir el modal de creación
    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    // Método para cerrar el modal de creación
    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }
    
    public function refreshUserList()
    {
        // No es necesario poner código aquí. El simple hecho de que el método
        // se ejecute es suficiente para que Livewire refresque el componente.
    }
    public function mount()
    {
        // Cargar todos los roles una sola vez cuando el componente se monta
        $this->allRoles = Role::all();
    }
    
    // Método para abrir el modal y cargar los datos del usuario
    public function editUser(User $user)
    {
        $this->editingUser = $user;
        $this->editingUserData = [
        'name' => $user->name,
        'email' => $user->email,
        'extension_sip' => $user->extension_sip,
        ];
        $this->userRoles = $user->roles->pluck('id')->toArray();
        $this->editingUserPassword = '';
        $this->editingUserPassword_confirmation = '';
        $this->showEditModal = true;
    }

    // Método para guardar los cambios
    public function updateUser()
{
    $rules = [
        'editingUserData.name' => 'required|string|max:255',
        'editingUserData.email' => 'required|email|unique:users,email,' . $this->editingUser->id,
        'editingUserData.extension_sip' => 'nullable|string|unique:users,extension_sip,' . $this->editingUser->id,
        'userRoles' => 'required|array|min:1',
        'userRoles.*' => 'exists:roles,id',
    ];
    if ($this->editingUserPassword) {
        $rules['editingUserPassword'] = 'required|string|min:8|confirmed';
    }
    $this->validate($rules);

    $this->editingUser->fill($this->editingUserData);
    if ($this->editingUserPassword) {
        $this->editingUser->password = \Hash::make($this->editingUserPassword);
    }
    $this->editingUser->save();
    $this->editingUser->roles()->sync($this->userRoles);

    session()->flash('message', 'Usuario actualizado correctamente.');
    $this->closeEditModal();
}

    // Método para cerrar el modal
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingUser = null;
        $this->userRoles = [];
        $this->editingUserPassword = '';
        $this->editingUserPassword_confirmation = '';
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::with('roles')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users' => $users,
        ]);
    }
}