<?php
namespace App\Http\Controllers;

use App\Traits\ApiResponsesTrait;
use App\Traits\HelpersTrait;

class BaseController extends Controller
{
    use HelpersTrait;
    use ApiResponsesTrait;
}