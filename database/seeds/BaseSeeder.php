<?php

use Illuminate\Database\Seeder;

use App\Helpers\Config;

class BaseSeeder extends Seeder
{
    public function processTitleSlug($string) : string
    {
        return Config::processTitleSlug($string);
    }

    public function sortUnitsByValue($query, $param)
    {
        return Config::sortUnitsByValue($query, $param);
    }
}