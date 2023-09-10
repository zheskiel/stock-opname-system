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
        $this->call(NewDataSeeder::class);
        $this->call(MasterDataSeeder::class);
    }
}
