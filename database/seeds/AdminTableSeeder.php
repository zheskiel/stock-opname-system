<?php

use App\Models\Admin;

class AdminTableSeeder extends BaseSeeder
{
    private $admin;

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }

    public function run()
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
}