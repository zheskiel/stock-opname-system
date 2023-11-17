<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;

use App\Models\{
    Outlet
};

use App\Traits\HelpersTrait;

class OutletController extends BaseController
{
    use HelpersTrait;

    private $outlet;

    public function __construct(
        Outlet $outlet
    ) {
        $this->outlet = $outlet;
    }

    public function FetchOutlets()
    {
        $outlets = $this->outlet->with(['manager'])->get();

        return $this->respondWithSuccess($outlets);
    }
}