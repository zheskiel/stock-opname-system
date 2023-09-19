<?php

use Illuminate\Database\Seeder;
use App\Models\MenuGroup;
use App\Models\MenuItem;

class MenuGeneralSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $general = MenuGroup::create([
            'name' => 'General',
            'permission_name' => 'general',
            'position' => 1,
        ]);

        MenuItem::create([
            'name' => 'Dashboard',
            'icon' => 'ri-dashboard-2-line',
            'route' => 'dashboard.index',
            'permission_name' => 'dashboard_index',
            'menu_group_id' => $general->id,
            'position' => 1,
        ]);
    }
}
