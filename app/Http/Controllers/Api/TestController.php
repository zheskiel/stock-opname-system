<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;

class TestController extends BaseController
{
    public function testStaffPage()
    {
        $this->middleware('guest:staff')->except('logout');

        $params = [
            'message' => "Staff Success"
        ];

        return $this->respondWithSuccess($params);
    }

    public function testManagerPage()
    {
        $this->middleware('guest:manager')->except('logout');

        $params = [
            'message' => "Manager Success"
        ];

        return $this->respondWithSuccess($params);
    }

    public function testAdminPage()
    {
        $this->middleware('guest:admin')->except('logout');

        $params = [
            'message' => "Admin Success"
        ];

        return $this->respondWithSuccess($params);
    }
}