<?php
namespace App\Services;

use App\Helpers\Config;

class BaseService
{
    public function processTitleSlug($data)
    {
        return Config::processTitleSlug($data);
    }
}