<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'admin']);

        Permission::create(['name' => 'article.view']);
        Permission::create(['name' => 'article.create']);
        Permission::create(['name' => 'article.edit']);
        Permission::create(['name' => 'article.create.note']);
        Permission::create(['name' => 'article.delete.note']);
        Permission::create(['name' => 'article.create.file']);
        Permission::create(['name' => 'article.delete.file']);

        Permission::create(['name' => 'order.view']);
        Permission::create(['name' => 'order.create']);
        Permission::create(['name' => 'order.edit']);
        Permission::create(['name' => 'order.delete']);
        Permission::create(['name' => 'order.add.delivery']);

        Permission::create(['name' => 'ordermessage.view']);
        Permission::create(['name' => 'ordermessage.create']);
        Permission::create(['name' => 'ordermessage.edit']);
        Permission::create(['name' => 'ordermessage.delete']);

        Permission::create(['name' => 'supplier.view']);
        Permission::create(['name' => 'supplier.create']);
        Permission::create(['name' => 'supplier.edit']);
        Permission::create(['name' => 'supplier.delete']);

        Permission::create(['name' => 'inventory.view']);
        Permission::create(['name' => 'inventory.create']);
        Permission::create(['name' => 'inventory.edit']);
        Permission::create(['name' => 'inventory.delete']);

        Permission::create(['name' => 'reports.view']);

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
    }
}
