<?php

use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    public function processTitleSlug($string)
    {
        return strtolower(preg_replace('~[^\p{L}\p{N}\n]+~u', '-', $string));
    }
}