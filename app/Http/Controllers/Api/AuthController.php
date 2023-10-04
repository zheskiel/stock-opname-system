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
    public function login(Request $request, $userType)
    {
        $guardType = "$userType-api";

        return $this->proceedLogin($request, $guardType);
    }

    private function proceedLogin($request, $guardType)
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
            $token = Auth::guard($guardType)->attempt($credentials);

            if (!$token) {
                return $this->respondUnAuthenticated('We cant find an account with this credentials.');
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->respondError("Failed to login, please try again.");
        }

        $authUser = Auth::guard($guardType)->user();
        $params = [
            'token' => $token,
            'user' => $authUser
        ];

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
        $items = ['admin', 'manager', 'staff'];

        foreach ($items as $item) {
            if (Auth::guard($item . '-api')->check()) {
                $token = Auth::guard($item . '-api')->refresh();
            }
        }

        $params = [ 'token' => $token ];

        return $this->respondWithSuccess($params);
    }

    public function TestToken()
    {
        $user = JWTAuth::parseToken()->authenticate();

        return $this->respondWithSuccess($user);
    }
}