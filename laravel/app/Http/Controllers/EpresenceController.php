<?php

namespace App\Http\Controllers;

use App\Models\Epresence;
use JWTAuth;
use Tymon\JWTAuth\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EpresenceController extends JWT
{
  //
  public function insert(Request $request)
  {
    $user = JWTAuth::parseToken()->authenticate();

    if ($user->npp_supervisor == null) return response()->json([
      'success' => false,
      'message' => 'Supervisor not allowed !'
    ], 422);

    $validator = Validator::make($request->all(), [
      'type' => 'required|in:IN,OUT',
      'waktu' => 'required|date'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => config('app.debug') ? $validator->errors() : ['error' => ['Request not valid !']],
      ], 422);
    }

    $date = explode(" ", $request->waktu)[0];
    $find = Epresence::where('type', $request->type)
      ->where("waktu", "LIKE", "$date%")
      ->where("id_users", $user->id)
      ->count();

    if ($find == 0) {
      if ($request->type == "OUT") {
        $findIN = Epresence::where('type', "IN")
          ->where("waktu", "LIKE", "$date%")
          ->where("id_users", $user->id)
          ->first();

        if(!$findIN) {
          return response()->json([
            'success' => true,
            'message' => "Failed insert data, absent IN needed before $request->type !"
          ], 422);
        }

        if(strtotime($findIN->waktu) > strtotime($request->waktu)) {
          return response()->json([
            'success' => true,
            'message' => "Failed insert data, absent $request->type must greater than IN !"
          ], 422);
        }
      }

      $insert = Epresence::insert([
        'id_users' => $user->id,
        'type' => $request->type,
        'waktu' => $request->waktu,
        'is_approve' => false
      ]);

      return response()->json([
        'success' => true,
        'message' => "Success insert data !",
        'type' => $request->type,
        'waktu' => $request->waktu
      ]);
    } else {
      return response()->json([
        'success' => true,
        'message' => "Failed insert data, user already absent $request->type !"
      ], 422);
    }
  }
  public function approve($id = "")
  {
    $user = JWTAuth::parseToken()->authenticate();

    if ($user->npp_supervisor !== null) return response()->json([
      'success' => false,
      'message' => 'User not allowed !'
    ], 422);

    if (!is_numeric($id)) {
      return response()->json([
        'success' => false,
        'message' => ['error' => ['Request not valid !']],
      ], 422);
    }

    $find = Epresence::selectRaw("epresence.*, users.npp_supervisor")
                      ->join("users", "epresence.id_users", "users.id")
                      ->where("epresence.id", $id)
                      ->first();

    if(!$find) {
      return response()->json([
        'success' => false,
        'message' => ['error' => ['Epresence not found !']],
      ], 422);
    }

    if($find->npp_supervisor != $user->npp) {
      return response()->json([
        'success' => false,
        'message' => ['error' => ['You are not allowed to approve this epresence !']],
      ], 422);
    }

    $approve = Epresence::where("id", $id)->update(['is_approve' => true]);

    return response()->json([
      'success' => $approve,
      'message' => ($approve ? 'Success' : 'Failed') . " approve data !"
    ]);
  }
  public function getData(Request $request)
  {
    $user = JWTAuth::parseToken()->authenticate();

    if ($user->npp_supervisor == null) return response()->json([
      'success' => false,
      'message' => 'Supervisor not allowed !'
    ], 422);

    $date = !isset($request->tanggal) ? date('Y-m-d') : $request->tanggal;
    $data = Epresence::where("id_users", $user->id)
                      ->where("waktu", "LIKE", "$date%")
                      ->get();

    if(count($data) == 0) return response()->json([
      'success' => false,
      'message' => 'No data found !'
    ], 422);

    $response = [];
    $response['nama_user'] = $user->nama;
    $response['id_user'] = $user->id;
    $response['tanggal'] = isset($data[0]->waktu) ? explode(" ", $data[0]->waktu)[0] : null;
    $response['waktu_masuk'] = isset($data[0]->waktu) ? explode(" ", $data[0]->waktu)[1] : null;
    $response['waktu_pulang'] = isset($data[1]->waktu) ? explode(" ", $data[1]->waktu)[1] : null;
    $response['status_masuk'] = isset($data[0]->is_approve) && $data[0]->is_approve ? "APPROVE" : "REJECT";
    $response['status_pulang'] = isset($data[1]->is_approve) ? ($data[1]->is_approve ? "APPROVE" : "REJECT") : null;
    
    return response()->json([
      'success' => true,
      'message' => 'Success get data',
      'data' => [$response]
    ]);
  }
}
