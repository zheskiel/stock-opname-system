<?php

use App\Models\Admin;
use App\Models\Manager;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() : void
    {
        Model::unguard();

        // Create Roles
        $superadmin = Role::create(['name' => 'superadmin']);
        $manager = Role::create(['name' => 'manager']);
        $staff = Role::create(['name' => 'staff']);

        // Assign Permissions To Roles
        $superadmin->givePermissionTo(Permission::all());
        $manager->givePermissionTo(['general', 'dashboard_index', 'profile_index']);
        $staff->givePermissionTo(['general', 'dashboard_index']);

        // Assign Roles To Users
        $admin = Admin::where('email', 'admin1@gmail.com')->first();
        $admin->assignRole('superadmin');

        $manager = Manager::where('email', 'manager-1@gmail.com')->first();
        $manager->assignRole('manager');

        $staff = Staff::where('email', 'staff-1@gmail.com')->first();
        $staff->assignRole('staff');
    }
}
