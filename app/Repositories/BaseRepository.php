<?php
namespace App\Repositories;

use App\Helpers\Config;

class BaseRepository
{
    public function processTitleSlug($data)
    {
        return Config::processTitleSlug($data);
    }
}