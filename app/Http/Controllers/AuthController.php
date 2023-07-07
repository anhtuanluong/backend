<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response(['message' => 'Success!', 'user' => $user]);

    }
    /**
     * Login user and create token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => date('Y-m-d H:i:s', strtotime('+3 minutes')),
            'refresh_token_expires' => date('Y-m-d H:i:s', strtotime('+4 minutes')),
            'user' => auth()->user()
        ]);
    }

    public function refreshToken(Request $request){
        $refreshToken = $request->get('Authorization');

        var_dump($refreshToken);

        if(! $refreshToken->Authorization && auth()->user()){
//        return $this->createNewToken($refreshToken);
        } else {
            return response(['error' => 'Unauthorized'], 401);

        }
    }

    public function logout(){
        try {
            auth()->logout();
            return response()->json(["success" => true, "msg" => "User logged out!"]);
        } catch (\Exception $e){
            return response()->json(["success" => false, "msg" => $e->getMessage()]);
        }

    }
}
