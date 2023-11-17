<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\{
    Supervisor
};

use App\Traits\HelpersTrait;

class SupervisorController extends BaseController
{
    use HelpersTrait;

    private $supervisor;

    public function __construct(
        Supervisor $supervisor
    ) {
        $this->supervisor = $supervisor;
    }

    public function FetchSupervisorsByOutlet(Request $request)
    {
        $outletId = $request->get('outletId');

        $supervisors = $this->supervisor
            ->with(['manager'])
            ->where('outlet_id', $outletId)
            ->get();

        return $this->respondWithSuccess($supervisors);
    }
}