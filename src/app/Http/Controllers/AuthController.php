<?php

namespace App\Http\Controllers;

use App\models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //ユーザー登録
  public function register(Request $request) {
    $rules = [
      "name" => ["required", "string", "max:50"],
      "email" => ["required", "string", "email", "max:255", "unique:users"],
      "password" => ["required", "string", "min:8", "max:50"],
    ];
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
    }

    $data = $request->only(["name", "email", "password"]);

    $user = User::create([
        "name" => $data->name,
        "email" => $data->email,
        "password" => Hash::make($data["password"]),
    ]);
    $json = [
        "data" => $user,
    ];
    return response()->json($json, Response::HTTP_OK);
  }
}