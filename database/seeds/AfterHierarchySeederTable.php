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

class AfterHierarchySeederTable extends Seeder
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
        $permissionList = [];
        $allPermissions = $this->permission->get()->pluck('name');
        foreach ($allPermissions as $permission) {
            $permissionList[] = [
                'name' => $permission
            ];
        }

        return [
            [
                'guard_name' => $guards['staff'],
                'name' => 'staff',
                'permissions' => [
                    ['name' => 'dashboard'],
                    ['name' => 'forms'],
                ],
                'model' => $models['staff'],
                'email' => "head-production-cook-staff-1@gmail.com"
            ],
            [
                'guard_name' => $guards['staff'],
                'name' => 'staff',
                'permissions' => [
                    ['name' => 'dashboard'],
                    ['name' => 'forms'],
                ],
                'model' => $models['staff'],
                'email' => "central-kitchen-supervisor-store-keeper-staff-1@gmail.com"
            ],
            [
                'guard_name' => $guards['admin'],
                'name' => 'superadmin',
                'permissions' => $permissionList,
                'model' => $models['admin'],
                'email' => "superadmin@gmail.com"
            ],
            [
                'guard_name' => $guards['admin'],
                'name' => 'admin',
                'permissions' => $permissionList,
                'model' => $models['admin'],
                'email' => "admin1@gmail.com"
            ],
            [
                'guard_name' => $guards['manager'],
                'name' => 'manager',
                'permissions' => [
                    ['name' => 'dashboard'],
                    ['name' => 'master'],
                    ['name' => 'templates'],
                    ['name' => 'template.create'],
                    ['name' => 'template.view'],
                    ['name' => 'template.edit'],
                    ['name' => 'forms'],
                    ['name' => 'form.create'],
                    ['name' => 'form.details'],
                    ['name' => 'form.edit'],
                    ['name' => 'report'],
                    ['name' => 'combined'],
                    ['name' => 'compare'],
                    ['name' => 'final'],
                ],
                'model' => $models['manager'],
                'email' => "manager-1@gmail.com"
            ],
            [
                'guard_name' => $guards['manager'],
                'name' => 'manager',
                'permissions' => [
                    ['name' => 'dashboard'],
                    ['name' => 'master'],
                    ['name' => 'templates'],
                    ['name' => 'template.create'],
                    ['name' => 'template.view'],
                    ['name' => 'template.edit'],
                    ['name' => 'forms'],
                    ['name' => 'form.create'],
                    ['name' => 'form.details'],
                    ['name' => 'form.edit'],
                    ['name' => 'report'],
                    ['name' => 'combined'],
                    ['name' => 'compare'],
                    ['name' => 'final'],
                ],
                'model' => $models['manager'],
                'email' => "manager-2@gmail.com"
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

            // Find Roles
            $role = $this->role
                ->where('guard_name', $guard)
                ->where('name', $name)
                ->first();

            // Assign Permissions To Roles
            foreach ($permissions as $permission) {
                $target = $this->permission->findByName($permission['name'], $guard);

                $role->givePermissionTo($target);
            }

            // Assign Permissions To Roles
            $user = $model->where('email', $email)->first();

            $user->assignRole($name);
        }
    }
}
