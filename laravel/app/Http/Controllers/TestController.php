<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\Manager;
use Tymon\JWTAuth\Http\Parser\Parser;
use JWTAuth;

class TestController extends JWT {
  public function __construct(Manager $manager, Parser $parser)
  {
      parent::__construct($manager, $parser);
  }

  public function index() {
    return response()->json([
      'response' => true
    ]);
  }
}