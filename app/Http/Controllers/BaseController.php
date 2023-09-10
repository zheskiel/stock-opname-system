<?php
namespace App\Http\Controllers;

use App\Helpers\Config;

class BaseController extends Controller
{
    public $limit = 10;

    public function processTitleSlug($string) : string
    {
        return Config::processTitleSlug($string);
    }

    public function getUserIpAddress($ipaddress = '') : string
    {
        return Config::getUserIpAddress($ipaddress);
    }

    public function generatePagination($itemData, $itemCount, $perPage, $page)
    {
        return Config::generatePagination($itemData, $itemCount, $perPage, $page);
    }

    public function customPaginate($items, $itemCount, $perPage = 15, $page = null, $options = [])
    {
        return Config::customPaginate($items, $itemCount, $perPage, $page, $options);
    }
}