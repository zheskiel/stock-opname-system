<?php

use App\Models\{
    Admin
};

class AdminTableSeeder extends BaseSeeder
{
    private $admin;

    public function __construct(
        Admin $admin
    ) {
        $this->admin = $admin;
    }

    private function createSuperAdmin()
    {
        $name = "superadmin";
        $params = [
            'name'      => $name,
            'slug'      => $this->processTitleSlug($name),
            'email'     => "$name@gmail.com",
            'password'  => bcrypt('test123')
        ];

        $this->admin->create($params);
    }

    private function createAdmins()
    {
        $adminLimit = 1;

        for ($x=1; $x <= $adminLimit; $x++) {
            $name = "admin $x";

            $params = [
                'name'      => $name,
                'slug'      => $this->processTitleSlug($name),
                'email'     => "admin$x@gmail.com",
                'password'  => bcrypt('test123')
            ];

            $this->admin->create($params);
        }
    }

    public function run()
    {
        $this->createSuperAdmin();
        $this->createAdmins();
    }
}