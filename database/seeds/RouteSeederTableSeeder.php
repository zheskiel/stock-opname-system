<?php

use App\Models\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class RouteSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Route::insert([
            [
                'route' => 'master.index',
                'permission_name' => 'master_products_view'
            ]
        ]);

        Route::insert([
            [
                'route' => 'test',
                'permission_name' => 'test'
            ]
        ]);

        Route::insert([
            [
                'route' => 'test_supervisor',
                'permission_name' => 'test_supervisor'
            ]
        ]);


        // General Setting
        /*
        Route::insert([
            [
                'route' => 'setting.index',
                'permission_name' => 'setting_index'
            ],
            [
                'route' => 'setting.update',
                'permission_name' => 'setting_update'
            ],
        ]);
        */
    }
}
