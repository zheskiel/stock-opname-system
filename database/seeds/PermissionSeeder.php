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
            // brands
            'brands_create',
            'brands_view',
            'brands_edit',

            // master products
            'master_products_create',
            'master_products_view',
            'master_products_edit',
            'master_products_delete',

            // templates
            'templates_view',

            // template
            'template_create',
            'template_view',
            'template_edit',
            'template_delete',

            // forms
            'forms_view',

            // form
            'form_create',
            'form_view',
            'form_edit',
            'form_delete',

            'form_submit_form',
            'form_review_form'
        ];

        $userLists = $this->getUserLists();

        Permission::create([
            'guard_name' => "admin-api",
            'name' => "test"
        ]);

        Permission::create([
            'guard_name' => "staff-api",
            'name' => "test_supervisor"
        ]);

        Permission::create([
            'guard_name' => "manager-api",
            'name' => "test_supervisor"
        ]);

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
