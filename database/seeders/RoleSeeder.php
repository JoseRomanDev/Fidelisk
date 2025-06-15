<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{

    public function run(): void
{
    $rolesData = [
        ['name' => 'agente', 'description' => 'Rol de Agente de Fidelisk'],
        ['name' => 'supervisor', 'description' => 'Rol de Supervisor de Fidelisk'],
        ['name' => 'admin', 'description' => 'Rol de Administrador de Fidelisk'],
    ];

    foreach ($rolesData as $roleData) {
        $role = Role::firstOrCreate(['name' => $roleData['name']], ['description' => $roleData['description']]);
        if ($role->wasRecentlyCreated) {
            $this->command->info("Rol '{$roleData['name']}' CREADO.");
        } elseif ($role->exists) {
            $this->command->info("Rol '{$roleData['name']}' YA EXISTÍA.");
            // Opcional: verificar si la descripción coincide y actualizar si es necesario
            if ($role->description !== $roleData['description']) {
                $role->description = $roleData['description'];
                $role->save();
                $this->command->warn("Descripción del rol '{$roleData['name']}' ACTUALIZADA.");
            }
        } else {
            $this->command->error("FALLO al crear/encontrar rol '{$roleData['name']}'.");
        }
    }
}
         
         

}
