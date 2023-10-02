<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\Manager;
use Tymon\JWTAuth\Http\Parser\Parser;
use JWTAuth;

class AuthController extends JWT
{
    public function __construct(Manager $manager, Parser $parser)
    {
        parent::__construct($manager, $parser);
    }

    public function login(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'email'  => 'required|email',
        'password' => 'required'
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => config('app.debug') ? $validator->errors() : ['error' => ['Request not valid !']],
        ], 422);
      }

      $attemptLogin = Auth::attempt(array(
        'email' => $request->email,
        'password' => $request->password
      ));

      if(!$attemptLogin) return response()->json([
        'success' => false,
        'message' => 'User not found !'
      ], 401);

      $user = User::where("email", $request->email)->first();

      $payload = $this->makePayload($user);
      $token = $this->manager->encode($payload)->get();

      return response()->json([
          'success' => true,
          'token' => $token,
          'expiredIn' => date('Y-m-d H:i:s', (time()+(env('JWT_LIMIT_MINUTES', 30)*60))),
          // 'expiredInTime' => strtotime(date('Y-m-d H:i:s', (time()+(env('JWT_LIMIT_MINUTES', 30)*60)))),
      ]);
    }
   
}