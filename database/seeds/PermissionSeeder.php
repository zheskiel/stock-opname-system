<?php

use App\Traits\HelpersTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    use HelpersTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Model::unguard();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard',
            'master',

            // Templates
            'templates',
            'template.create',
            'template.view',
            'template.edit',

            // Forms
            'forms',
            'form.create',
            'form.details',
            'form.edit',
        ];

        $userLists = $this->getUserLists();

        foreach ($userLists as $user) {
            foreach ($permissions as $permission)
            {
                Permission::create([
                    'guard_name' => "$user-api",
                    'name' => $permission
                ]);
            }
        }
    }
}
