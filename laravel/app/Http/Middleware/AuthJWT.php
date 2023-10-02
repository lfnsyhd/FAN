<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class AuthJWT extends BaseMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle($request, Closure $next)
  {
    try {
      $user = JWTAuth::parseToken()->authenticate();
      if (!$user) {
        throw new Exception('User Not Found');
      }

      // if ($request->is('api/backoffice*') && $user['LEVEL'] !== 2) {
      //   return response()->json([
      //     'success' => false,
      //     'message' => ['error' => 'Token Invalid']
      //   ]);
      // }
    } catch (Exception $e) {

      if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        return response()->json([
          'success' => false,
          'message' => ['error' => 'Token Invalid']
        ]);
      } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        return response()->json([
          'success' => false,
          'message' => ['error' => 'Token Expired']
        ]);
      } else {
        if ($e->getMessage() === 'User Not Found') {
          return response()->json([
            'success' => false,
            'message' => ['error' => 'User Not Found']
          ]);
        }
        return response()->json([
          'success' => false,
          'message' => ['error' => 'Authorization Token not found']
        ]);
      }
    }

    return $next($request);
  }
}
