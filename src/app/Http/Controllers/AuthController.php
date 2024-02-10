<?php

namespace App\Http\Controllers;

use App\models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
  private $user;

  public function __construct() {
    $this->user = new User();
  }

  public function register(RegisterRequest $request) {
    $registerData = $request->validated();
    $user = $this->user->createUser($registerData);
    return response()->json($user, Response::HTTP_CREATED);
  }

  public function login(LoginRequest $request) {
    $loginData = $request->validated();
    if (Auth::attempt($loginData)) {
      $user = $this->user->loginUser($loginData);
      $token = $user->generateAuthToken();
      return response()->json(["token" => $token, "user" => $user], Response::HTTP_OK);
    }

    return response()->json('認証に失敗しました', Response::HTTP_UNAUTHORIZED);
  }

  public function logout(Request $request) {
    $request->user()->deleteAuthTokens();
    Auth::guard("web")->logout();
    $cookie = Cookie::forget('laravel_session');
    return response()->json('ログアウトしました', Response::HTTP_OK)->withCookie($cookie);
  }
}
