<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role; 
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener los roles de la base de datos
        $roleAgente = Role::where('id','1')->first();
        $roleSupervisor = Role::where('id','2')->first();
        $roleAdmin = Role::where('id','3')->first();

        // Verificar si los roles existen antes de intentar usarlos
        if (!$roleAgente || !$roleSupervisor || !$roleAdmin) {
            $this->command->error('Uno o más roles (agente, supervisor, admin) no fueron encontrados en la base de datos. Asegúrate de que RoleSeeder se haya ejecutado correctamente. El seeder de usuarios no se ejecutará.');
            return;
        }

        // 2. Crear/Actualizar Usuario Agente y asignarle EXCLUSIVAMENTE el rol de Agente
        $agenteUser = User::updateOrCreate(
            ['email' => 'agente@agente.laravel'],
            [
                'name' => 'AgenteTest',
                'password' => Hash::make('agente123'),
                'extension_sip' => '101',
            ]
        );
        // El evento 'created' del modelo User le asignará 'agente'.
        // Usamos sync() para asegurar que SOLO tenga este rol. Si el evento ya lo puso, sync() lo reafirma.
        $agenteUser->roles()->sync([$roleAgente->id]);

        // 3. Crear/Actualizar Usuario Supervisor y asignarle EXCLUSIVAMENTE el rol de Supervisor
        $supervisorUser = User::updateOrCreate(
            ['email' => 'supervisor@supervisor.laravel'],
            [
                'name' => 'SupervisorTest',
                'password' => Hash::make('supervisor123'),
                'extension_sip' => '102',
            ]
        );
        // El evento 'created' le asignará 'agente'.
        // sync() reemplazará cualquier rol existente (como 'agente') y le asignará SOLO 'supervisor'.
        $supervisorUser->roles()->sync([$roleSupervisor->id]);


        // 4. Crear/Actualizar Usuario Admin y asignarle EXCLUSIVAMENTE el rol de Admin
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@admin.laravel'],
            [
                'name' => 'AdminTest',
                'password' => Hash::make('admin123'),
                'extension_sip' => '100',
            ]
        );
        // El evento 'created' le asignará 'agente'.
        // sync() reemplazará cualquier rol existente (como 'agente') y le asignará SOLO 'admin'.
        $adminUser->roles()->sync([$roleAdmin->id]);

        $this->command->info('Usuarios de prueba (Agente, Supervisor, Admin) creados/actualizados y roles asignados exclusivamente.');
    }
}
