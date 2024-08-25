<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create permissions
        $create = Permission::create(['name' => 'create']);
        $read = Permission::create(['name' => 'read']);
        $edit = Permission::create(['name' => 'edit']);
        $update = Permission::create(['name' => 'update']);
        $delete = Permission::create(['name' => 'delete']);

        // Create roles
        $ingeneur = Role::create(['name' => 'ingeneur']);
        $admin = Role::create(['name' => 'admin']);
        $editor = Role::create(['name' => 'editor']);

        // Assign permissions to roles
        $ingeneur->permissions()->attach([$create->id, $read->id, $edit->id, $update->id, $delete->id]);
        $admin->permissions()->attach([$create->id, $read->id, $edit->id, $update->id, $delete->id]);
        $editor->permissions()->attach([$create->id, $read->id, $edit->id]);
    }
}
