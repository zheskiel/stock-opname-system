<?php

class DatabaseSeeder extends BaseSeeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminTableSeeder::class);
        $this->call(HierarchyDataSeeder::class);
        $this->call(MasterDataSeeder::class);
        $this->call(TemplateDataSeeder::class);
        $this->call(FormsTableSeeder::class);
        $this->call(FormItemsTableSeeder::class);

        $this->call(PermissionSeeder::class);
        $this->call(RoleSeederTable::class);
        $this->call(RouteSeederTableSeeder::class);

        $this->call(MenuGeneralSeederTableSeeder::class);
        $this->call(MenuSettingSeederTableSeeder::class);

        $this->call(ReportTableSeeder::class);
    }
}
