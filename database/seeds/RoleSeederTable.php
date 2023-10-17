<?php

use App\Models\{
    Admin,
    Manager,
    Staff
};

use Spatie\Permission\Models\{
    Permission, Role
};

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class RoleSeederTable extends Seeder
{
    private $permission;
    private $role;
    private $admin;
    private $manager;
    private $staff;

    public function __construct(
        Permission $permission,
        Manager $manager,
        Admin $admin,
        Staff $staff,
        Role $role
    ) {
        $this->permission = $permission;
        $this->manager = $manager;
        $this->admin = $admin;
        $this->staff = $staff;
        $this->role = $role;
    }

    private function fetchAllGuards()
    {
        return [
            'admin'      => 'admin-api',
            'manager'    => 'manager-api',
            'staff'      => 'staff-api'
        ];
    }

    private function fetchAllModels()
    {
        return [
            'admin'      => new $this->admin,
            'manager'    => new $this->manager,
            'staff'      => new $this->staff,
        ];
    }

    private function fetchAllItems($guards, $models)
    {
        $allPermissions = $this->permission
            ->where('name', '!=', 'test_supervisor')->get();

        return [
            [
                'guard_name' => $guards['admin'],
                'name' => 'superadmin',
                'permissions' => $allPermissions,
                'model' => $models['admin'],
                'email' => "superadmin@gmail.com"
            ],
            [
                'guard_name' => $guards['admin'],
                'name' => 'admin',
                'permissions' => [
                    ['name' => 'template_view']
                ],
                'model' => $models['admin'],
                'email' => "admin1@gmail.com"
            ],
            [
                'guard_name' => $guards['manager'],
                'name' => 'manager',
                'permissions' => [
                    ['name' => 'template_view']
                ],
                'model' => $models['manager'],
                'email' => "manager-1@gmail.com"
            ],
            [
                'guard_name' => $guards['staff'],
                'name' => 'staff',
                'permissions' => [
                    ['name' => 'template_view']
                ],
                'model' => $models['staff'],
                'email' => "head-production-cook-staff-1@gmail.com"
            ]
            ];
    }

    public function run() : void
    {
        Model::unguard();

        $items = $this->fetchAllItems(
            $this->fetchAllGuards(),
            $this->fetchAllModels()
        );

        foreach ($items as $item) {
            $permissions = $item['permissions'];
            $guard       = $item['guard_name'];
            $model       = $item['model'];
            $email       = $item['email'];
            $name        = $item['name'];

            // Create Roles
            $role = $this->role->create([
                'guard_name' => $guard,
                'name' => $name
            ]);

            // Assign Permissions To Roles
            foreach ($permissions as $permission) {
                $target = $this->permission->findByName($permission['name'], $guard);

                $role->givePermissionTo($target);
            }

            // Assign Permissions To Roles
            $user = $model->where('email', $email)->first();
            $user->assignRole($name);
        }

        // Manager
        $guard = "manager-api";
        $role = $this->role->where('name', 'manager')->first();

        $target = $this->permission->findByName("test_supervisor", $guard);
        $role->givePermissionTo($target);

        // Supervisor
        $guard = "staff-api";
        $role = $this->role->create([
            'guard_name' => $guard,
            'name' => "supervisor"
        ]);

        $target = $this->permission->findByName("test_supervisor", $guard);
        $role->givePermissionTo($target);

        // Assign Permissions To Roles
        $email = "head-production-cook-staff-1@gmail.com";

        $user = $model->where('email', $email)->first();
        $user->assignRole($name);
    }
}
