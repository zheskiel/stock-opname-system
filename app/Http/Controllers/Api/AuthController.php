<?php
namespace App\Http\Controllers\Api;

use Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    private $guardType;

    public function StaffLogin(Request $request)
    {
        config(['jwt.user', 'App\Models\Staff']);
        config(['auth.providers.users.model', Staff::class]);

        $this->guardType = "staff-api";

        return $this->proceedLogin($request);
    }

    public function ManagerLogin(Request $request)
    {
        config(['jwt.user', 'App\Models\Manager']);
        config(['auth.providers.users.model', Manager::class]);

        $this->guardType = "manager-api";

        return $this->proceedLogin($request);
    }

    public function AdminLogin(Request $request)
    {
        config(['jwt.user', 'App\Models\Admin']);
        config(['auth.providers.users.model', Admin::class]);

        $this->guardType = "admin-api";

        return $this->proceedLogin($request);
    }

    private function proceedLogin($request)
    {
        $credentials = $request->only('email', 'password');
        
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);

        if ($validator->fails()) {
            return $this->respondUnAuthenticated($validator->messages());
        }

         try {
            // attempt to verify the credentials and create a token for the user
            // $token = JWTAuth::attempt($credentials);
            $token = Auth::guard($this->guardType)->attempt($credentials);

            if (!$token) {
                return $this->respondUnAuthenticated('We cant find an account with this credentials.');
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->respondError("Failed to login, please try again.");
        }

        $params = [ 'token' => $token ];

        return $this->respondWithSuccess($params);
    }

    public function Logout(Request $request)
    {
        $items = ['admin', 'manager', 'staff'];

        foreach ($items as $item) {
            Auth::guard($item . '-api')->logout();
        }

        return $this->respondWithSuccess([
            'message' => "You have successfully logged out."
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function TestToken(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        return $this->respondWithSuccess($user);
    }
}